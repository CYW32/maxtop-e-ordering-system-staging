<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * List all roles. Fulfills Backbone 2.f (Custom Roles).
     */
    public function index()
    {
        $roles = Role::withCount('users')->get();
        // Core roles that cannot be deleted to maintain system logic integrity
        $protectedRoles = ['admin', 'cs_leader', 'cs_staff', 'customer'];

        return view('admin.roles.index', compact('roles', 'protectedRoles'));
    }

    /**
     * Fulfills Requirement: Block "Admin" role creation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:roles,name',
                // SECURITY GUARD: No one, including current admin, can create another admin role
                'not_in:admin,Admin,ADMIN',
            ],
        ]);

        Role::create(['name' => strtolower($request->name), 'guard_name' => 'web']);

        return redirect()->route('admin.roles.manage.index')->with('success', 'Custom role established.');
    }

    /**
     * Prevent deletion of system-critical roles.
     */
    public function destroy(Role $role)
    {
        $protectedRoles = ['admin', 'cs_leader', 'cs_staff', 'customer'];

        if (in_array($role->name, $protectedRoles)) {
            return redirect()->back()->with('error', 'System Integrity Violation: Protected roles cannot be removed.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->back()->with('error', 'Role is currently assigned to active users.');
        }

        $role->delete();

        return redirect()->route('admin.roles.manage.index')->with('success', 'Role removed.');
    }
}
