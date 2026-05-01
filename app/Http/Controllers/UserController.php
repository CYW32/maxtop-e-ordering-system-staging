<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate; // FIXED: Added missing facade import
// Added: Required for transaction safety
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // ARCHITECTURE FIX: Granular Authorization
        Gate::authorize('view_login_credentials');

        $roles = Role::pluck('name', 'name');
        $status = ['active', 'deactive'];

        // ARCHITECTURE FIX: Remove ->whereNull('parent_id') [Addendum 2.a]
        // We now list all Login Credentials, grouped by their linked company.
        $query = User::with(['roles', 'company']);

        if (auth()->user()->hasRole('cs_leader')) {
            $query->whereDoesntHave('roles', fn($q) => $q->whereIn('name', ['admin', 'cs_leader']));
        }

        if (
            auth()->user()->hasPermissionTo('view_assigned_customers') &&
            !auth()
                ->user()
                ->hasAnyRole(['admin', 'cs_leader'])
        ) {
            $query->where('assigned_cs_id', auth()->id());
        }

        $users = $query->search($request->search)->filterByDate()->filterByRole()->latest()->paginate(15)->withQueryString();

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

    /**
     * Fulfills Backbone 2.a & 3.b: Onboard Login Credentials.
     * ARCHITECTURE FIX: Resolved "Undefined variable $parent" by providing context.
     */
    public function create(Request $request)
    {
        Gate::authorize('create_users');

        // 1. Core Data Collections
        $roles = \Spatie\Permission\Models\Role::where('name', '!=', 'admin')->get();
        $companys = \App\Models\Company::orderBy('company_name')->get();
        $csStaffMembers = User::role(['admin', 'cs_leader', 'cs_staff'])->get();

        // 2. ARCHITECTURE FIX: Retrieve parent context if creating a Branch account [Backbone 3.a.2]
        // This resolves the ErrorException at line 4 of the create blade.
        $parent = $request->has('parent_id') ? User::find($request->parent_id) : null;

        return view('users.create', compact('roles', 'csStaffMembers', 'companys', 'parent'));
    }

    /**
     * ARCHITECTURE FIX: Conditional validation for 'company_id'.
     */
    public function store(Request $request)
    {
        Gate::authorize('create_users');

        $request->validate([
            'name' => 'required|string|max:255',
            'login_id' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
            'role' => 'required|exists:roles,name',
            // FIX: company_id is ONLY required if the user is a customer [Addendum 2.a]
            'company_id' => 'required_if:role,customer|nullable|exists:companys,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'login_id' => $request->login_id,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'company_id' => $request->company_id, // Null for non-customer roles
            'status' => 'active',
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'User login created.');
    }

    public function edit(User $user)
    {
        Gate::authorize('edit_users');

        if ($user->hasRole('admin') && auth()->id() !== $user->id) {
            abort(403, 'System Integrity Lock: Root Admin is immutable.');
        }

        $roles = \Spatie\Permission\Models\Role::where('name', '!=', 'admin')->get();
        $csStaffMembers = User::role(['admin', 'cs_leader', 'cs_staff'])->get();
        $companys = \App\Models\Company::orderBy('company_name')->get();

        // ARCHITECTURE FIX: Determine if assignment is locked based on order existence [Backbone 9.c.1]
        $isAssignmentLocked = $user->orders()->exists();

        return view('users.edit', compact('user', 'roles', 'csStaffMembers', 'companys', 'isAssignmentLocked'));
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
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,deactive',
            // FIX: company_id is ONLY required if the user being updated is a customer
            'company_id' => 'required_if:role,customer|nullable|exists:companys,id',
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
        if (!$user->hasRole('admin')) {
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

        if (
            auth()
                ->user()
                ->hasAnyRole(['admin', 'cs_leader'])
        ) {
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
        // 1. SAFEGUARD: Check if the user has associated orders
        if (!$user->canBeDeleted()) {
            return redirect()
                ->route('users.index') // or back()
                ->with('error', "Cannot delete {$user->name} because they have existing order transactions.");
        }

        // 2. Proceed with deletion
        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', "User ({$name}) deleted successfully.");
    }
}
