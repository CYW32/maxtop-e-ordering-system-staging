<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleManagerController extends Controller
{
    // 1. SHOW THE MATRIX
    public function index()
    {
        // OLD: $roles = Role::where('name', '!=', 'customer')->get();
        // NEW: Get ALL roles, including customer
        $roles = Role::all();

        // Get all Permissions (Features)
        $permissions = Permission::all();

        return view('admin.roles.matrix', compact('roles', 'permissions'));
    }

    // 2. UPDATE PERMISSIONS
    public function update(Request $request)
    {
        $matrix = $request->input('matrix', []);

        // OLD: $roles = Role::where('name', '!=', 'customer')->get();
        // NEW: Loop through ALL roles so we can save customer settings too
        $roles = Role::all();

        foreach ($roles as $role) {
            // Get the list of permission IDs checked for this specific role
            $permissionsForRole = $matrix[$role->id] ?? [];

            // Get names based on IDs
            $permissionNames = Permission::whereIn('id', $permissionsForRole)->pluck('name');

            // Sync the permissions
            $role->syncPermissions($permissionNames);
        }

        return redirect()->back()->with('success', 'Permissions updated successfully!');
    }
}
