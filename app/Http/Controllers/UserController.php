<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // 1. LIST USERS
    public function index()
    {
        // Get all users with their roles attached
        // paginate(10) means show 10 users per page
        $users = User::with('roles')->paginate(10);

        return view('users.index', compact('users'));
    }

    // 2. SHOW CREATE FORM
    public function create()
    {
        // Get all roles except 'admin' if you want to restrict that
        // For now, let's fetch all available roles so you can choose
        $roles = Role::all();

        return view('users.create', compact('roles'));
    }

    // 3. STORE NEW USER
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
        // Get roles for the dropdown
        $roles = Role::all();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,deactive',
            'password' => 'nullable|min:8', // <--- Allow null, but if present must be 8+
        ]);

        // 1. Prepare data to update
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status,
        ];

        // 2. Logic: Only hash and add password if user typed something
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // 3. Update User
        $user->update($data);

        // 4. Sync Roles
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }
}
