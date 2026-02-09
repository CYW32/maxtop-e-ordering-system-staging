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
        $roles = Role::pluck('name', 'name');
        $status = ['active', 'deactive'];

        // ARCHITECTURE FIX: Remove ->whereNull('parent_id') [Addendum 2.a]
        // We now list all Login Credentials, grouped by their linked company.
        $query = User::with(['roles', 'company']);

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

        return view('users.index', compact('users', 'roles', 'status'));
    }

    public function assignedIndex(Request $request)
    {
        // ARCHITECTURE FIX: Remove ->whereNull('parent_id') [Addendum 2.a]
        // This fulfills Section 5.a: Viewing assigned customer logins.
        $users = User::where('assigned_cs_id', auth()->id())
            ->with(['roles', 'company'])
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
        if ($request->has('parent_id')) {
            $parent = User::findOrFail($request->parent_id);
        }

        // ARCHITECTURE FIX: Fetch from Company model (v1.4 Rename) [1.a]
        $companys = \App\Models\Company::orderBy('company_name')->get();
        $catalogs = \App\Models\Catalog::all();
        $csStaffMembers = User::role(['admin', 'cs_leader', 'cs_staff'])->get();

        return view('users.create', compact('roles', 'parent', 'catalogs', 'csStaffMembers', 'companys'));
    }

    // Save new user
    public function store(Request $request)
    {
        // Fulfills Addendum 3.b: User Management ONLY for Name, Email, Password, and Company Link
        $request->validate([
            'name' => 'required|string|max:255',
            'login_id' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
            'company_id' => 'required|exists:companys,id', // Mandatory link to a pre-existing business
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'login_id' => $request->login_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $request->company_id,
            'status' => 'active',
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'User login created.');
    }

    public function edit(User $user)
    {
        if (auth()->user()->hasPermissionTo('edit_assigned_customers') &&
            ! auth()->user()->hasAnyRole(['admin', 'cs_leader'])) {
            if ($user->assigned_cs_id !== auth()->id()) {
                abort(403, 'Unauthorized.');
            }
        }

        $roles = \Spatie\Permission\Models\Role::all();
        $csStaffMembers = User::role(['admin', 'cs_leader', 'cs_staff'])->get();

        // NEW: Pass Companies to Edit view for reassignment [Addendum 3.b]
        $companys = \App\Models\Company::orderBy('company_name')->get();

        return view('users.edit', compact('user', 'roles', 'csStaffMembers', 'companys'));
    }

    /**
     * Fulfills Addendum 3.b: User Management ONLY for Credentials and Company Link.
     * ARCHITECTURE FIX: Purged legacy 'details()' relationship call.
     */
    public function update(Request $request, User $user)
    {
        // 1. Validation scoped strictly to User Credential attributes [Addendum 3.b]
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,deactive',
            'company_id' => 'required|exists:companys,id', // Fulfills Many-to-One link [2.a]
            'assigned_cs_id' => 'nullable|exists:users,id',
            'password' => 'nullable|min:8|confirmed',
        ]);

        // 2. Prepare Core User Data
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'company_id' => $request->company_id, // Direct link to business entity
        ];

        // 3. Security Guard: Only allow status/role changes if target is NOT an admin
        if (! $user->hasRole('admin')) {
            $userData['status'] = $request->status;

            if (auth()->user()->hasPermissionTo('reassign_customers')) {
                $userData['assigned_cs_id'] = $request->assigned_cs_id;
            }

            $user->syncRoles([$request->role]);
        }

        // 4. Handle Optional Password Update
        if ($request->filled('password')) {
            $userData['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        // 5. Atomic Update to Users Table
        $user->update($userData);

        // ARCHITECTURE FIX: The legacy $user->details() block has been REMOVED.
        // Business data (PIC, Address, etc.) must be updated via CompanyController [3.c].

        if (auth()->user()->hasAnyRole(['admin', 'cs_leader'])) {
            return redirect()->route('users.index')->with('success', 'User credentials updated successfully.');
        }

        return redirect()->route('users.assigned')->with('success', 'Customer login updated.');
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
