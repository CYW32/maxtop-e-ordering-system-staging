<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // // 1. LIST USERS
    // public function index(Request $request)
    // {
    //     // Get Roles for the filter dropdown
    //     // plucking name/name creates an array like ['admin' => 'admin']
    //     $roles = Role::pluck('name', 'name');

    //     $users = User::with('roles')
    //         ->search($request->search) // <--- Magic happens here(The 'search()' method now exists automatically!)
    //         ->filterByDate() // <--- Date Filter (Defaults to created_at)
    //         ->filterByRole()
    //         ->latest()
    //         ->paginate(10)
    //         ->withQueryString(); // Keep the search term when changing pages

    //     return view('users.index', compact('users', 'roles'));
    // }

    public function index(Request $request)
    {
        // Get Roles for the filter dropdown
        // plucking name/name creates an array like ['admin' => 'admin']
        $roles = Role::pluck('name', 'name');

        $query = User::with('roles');

        // CS Leaders cannot see Admins or other CS Leaders [7]
        if (auth()->user()->hasRole('cs_leader')) {
            $query->whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['admin', 'cs_leader']);
            });
        }

        // DATA ISOLATION LOGIC:
        // If the user is CS Staff and the 'view_assigned_customers' permission is ON
        if (auth()->user()->hasPermissionTo('view_assigned_customers') &&
            ! auth()->user()->hasAnyRole(['admin', 'cs_leader'])) {

            // They ONLY see customers assigned to them
            $query->where('assigned_cs_id', auth()->id());
        }

        $roles = Role::pluck('name', 'name');
        $users = $query->search($request->search)
            ->filterByDate()
            ->filterByRole()
            ->latest()
            ->paginate(10)
            ->withQueryString(); // Keep the search term when changing pages

        return view('users.index', compact('users', 'roles'));
    }

    public function assignedIndex(Request $request)
    {
        // Fetch only users where assigned_cs_id matches the logged-in staff member
        $users = User::where('assigned_cs_id', auth()->id())
            ->with('roles')
            ->search($request->search)
            ->filterByDate()
            ->latest()
            ->paginate(10);

        // We only need the 'customer' role for the filter in this view
        $roles = ['customer' => 'customer'];

        return view('users.assigned', compact('users', 'roles'));
    }

    // SHOW CREATE FORM
    public function create()
    {
        // Get all roles except 'admin' if you want to restrict that
        // For now, let's fetch all available roles so you can choose
        $roles = Role::all();

        return view('users.create', compact('roles'));
    }

    // STORE NEW USER
    public function store(Request $request)
    {
        // A. Validate the input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'login_id' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'], // Must pick a valid role
        ]);

        // B. Create the User
        $user = User::create([
            'name' => $request->name,
            'login_id' => $request->login_id,
            'email' => $request->email,
            'status' => 'active', // Default status
            'password' => Hash::make($request->password),
        ]);

        // C. Assign the Role (Spatie Logic)
        $user->assignRole($request->role);

        // D. Go back to list with success message
        return redirect()->route('users.index')
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

        return view('users.edit', compact('user', 'roles', 'csStaffMembers'));
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
            // NEW: Validation for customer_details
            'company_name' => 'nullable|string|max:255',
            'company_reg_no' => 'nullable|string|max:255',
            'pic_name' => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:255',
            'delivery_address' => 'nullable|string',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
        ]);

        // Update User Account Data
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status,
        ];

        if (auth()->user()->hasPermissionTo('reassign_customers')) {
            $userData['assigned_cs_id'] = $request->assigned_cs_id;
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
}
