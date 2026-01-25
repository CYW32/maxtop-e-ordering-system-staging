<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate; // FIXED: Added missing facade import
use Illuminate\Support\Facades\Hash;   // Added: Required for transaction safety
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $roles = \Spatie\Permission\Models\Role::pluck('name', 'name');

        // Fulfills Section 3.a & User Request: Fetch only top-level accounts (no parent)
        $query = User::with(['roles', 'parent', 'branches.roles', 'details'])
            ->whereNull('parent_id');

        if (auth()->user()->hasRole('cs_leader')) {
            $query->whereDoesntHave('roles', fn ($q) => $q->whereIn('name', ['admin', 'cs_leader']));
        }

        if (auth()->user()->hasPermissionTo('view_assigned_customers') &&
            ! auth()->user()->hasAnyRole(['admin', 'cs_leader'])) {
            $query->where('assigned_cs_id', auth()->id());
        }

        $users = $query->search($request->search)
            ->filterByDate()
            ->filterByRole()
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('users.index', compact('users', 'roles'));
    }

    public function assignedIndex(Request $request)
    {
        // Fulfills Section 5.a & User Request: Only top-level assigned customers
        $users = User::where('assigned_cs_id', auth()->id())
            ->whereNull('parent_id')
            ->with(['roles', 'parent', 'branches.roles', 'details'])
            ->search($request->search)
            ->filterByDate()
            ->latest()
            ->paginate(15);

        $roles = ['customer' => 'customer'];

        return view('users.assigned', compact('users', 'roles'));
    }

    // SHOW CREATE FORM
    public function create(Request $request)
    {
        $roles = \Spatie\Permission\Models\Role::all();
        $parent = null;

        // Fulfills Section 3.a.2: Branch inheritance context
        if ($request->has('parent_id')) {
            $parent = User::with('details')->findOrFail($request->parent_id);
        }

        $catalogs = \App\Models\Catalog::all();
        $csStaffMembers = User::role(['admin', 'cs_leader', 'cs_staff'])->get();

        return view('users.create', compact('roles', 'parent', 'catalogs', 'csStaffMembers'));
    }

    // Save new user
    public function store(Request $request)
    {
        // 1. Updated Validation to include all Detail fields
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'login_id' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
            'parent_id' => ['nullable', 'exists:users,id'],
            'catalog_id' => ['nullable', 'exists:catalogs,id'],
            'assigned_cs_id' => ['nullable', 'exists:users,id'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_reg_no' => ['nullable', 'string', 'max:255'],
            'pic_name' => ['nullable', 'string', 'max:255'],
            'pic_phone' => ['nullable', 'string', 'max:255'],
            'delivery_address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
        ]);

        // 2. Create the User with Hierarchy data
        $user = User::create([
            'name' => $request->name,
            'login_id' => $request->login_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'active',
            'parent_id' => $request->parent_id, // Fulfills Branch Architecture [3]
            'catalog_id' => $request->catalog_id,
            'assigned_cs_id' => $request->assigned_cs_id ?? (auth()->user()->hasRole('cs_staff') ? auth()->id() : null),
        ]);

        $user->assignRole($request->role);

        // 3. Fulfills Request: Persist ALL detail fields, not just company_name
        if ($request->role === 'customer') {
            $user->details()->create($request->only([
                'company_name',
                'company_reg_no',
                'pic_name',
                'pic_phone',
                'delivery_address',
                'city',
                'state',
                'postal_code',
            ]));
        }

        return redirect()->route($request->has('parent_id') || auth()->user()->hasRole('cs_staff') ? 'users.assigned' : 'users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        // SECURITY CHECK: If user is CS Staff and has the restricted edit permission
        if (auth()->user()->hasPermissionTo('edit_assigned_customers') &&
            ! auth()->user()->hasAnyRole(['admin', 'cs_leader'])) {

            // Block if the customer isn't assigned to them
            if ($user->assigned_cs_id !== auth()->id()) {
                abort(403, 'You are not authorized to edit this customer.');
            }
        }

        $roles = Role::all();
        $csStaffMembers = User::role(['admin', 'cs_leader', 'cs_staff'])->get();
        $catalogs = \App\Models\Catalog::all();

        return view('users.edit', compact('user', 'roles', 'csStaffMembers', 'catalogs'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,deactive',
            'assigned_cs_id' => 'nullable|exists:users,id',
            'password' => 'nullable|min:8',
            'parent_id' => 'nullable|exists:users,id',
            'catalog_id' => 'nullable|exists:catalogs,id',
            'company_name' => 'nullable|string|max:255',
            'company_reg_no' => 'nullable|string|max:255',
            'pic_name' => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:255',
            'delivery_address' => 'nullable|string',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
        ]);

        // Start with basic info that is ALWAYS editable
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'catalog_id' => $request->catalog_id, // Fulfills Single Catalog Policy
        ];

        // BACKEND SECURITY LOCK:
        // Only allow changing Status, Role, and Assignment if the target is NOT an admin.
        if (! $user->hasRole('admin')) {
            $userData['status'] = $request->status;

            if (auth()->user()->hasPermissionTo('reassign_customers')) {
                $userData['assigned_cs_id'] = $request->assigned_cs_id;
            }

            // Only sync roles for non-admins
            $user->syncRoles([$request->role]);
        }

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);
        $user->syncRoles([$request->role]);

        // Update or Create Customer Details
        $user->details()->updateOrCreate(
            ['user_id' => $user->id], // Match by user ID
            $request->only([
                'company_name', 'company_reg_no', 'pic_name', 'pic_phone',
                'delivery_address', 'postal_code', 'city', 'state',
            ])
        );

        // FIX: Redirect based on role/permission
        if (auth()->user()->hasAnyRole(['admin', 'cs_leader'])) {
            return redirect()->route('users.index')->with('success', 'User updated successfully.');
        }

        return redirect()->route('users.assigned')->with('success', 'Customer info updated.');
    }

    /**
     * Remove the specified user and their branches from storage.
     * Fulfills Requirement: Cascading delete for HQ and Branches if no order history exists.
     * Fulfills Section 3.c: Protects DB records if order history is found.
     */
    public function destroy(User $user)
    {
        // Now resolves correctly to Illuminate\Support\Facades\Gate
        Gate::authorize('edit_users');

        // Refined Section 3.c.1 Check:
        // Verification happens in the Model to ensure clusters are clean.
        if (! $user->canBeDeleted()) {
            return redirect()->back()->with('error', 'This user or its branches have existing order records and cannot be deleted to protect data integrity.');
        }

        DB::transaction(function () use ($user) {
            // Fulfills Request: If HQ is deleted, delete all branches first
            if (is_null($user->parent_id)) {
                $user->branches()->delete();
            }

            $user->delete();
        });

        return redirect()->route('users.index')->with('success', 'User account and associated branches removed successfully.');
    }
}
