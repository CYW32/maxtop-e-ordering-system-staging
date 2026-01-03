<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Create Permissions
        // We use firstOrCreate to prevent errors if you run seed multiple times
        Permission::firstOrCreate(['name' => 'view_users']);
        Permission::firstOrCreate(['name' => 'create_users']);
        Permission::firstOrCreate(['name' => 'edit_users']); // <--- NEW PERMISSION

        // 3. Create Roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $leader = Role::firstOrCreate(['name' => 'cs_leader']);
        $staff = Role::firstOrCreate(['name' => 'cs_staff']);
        $customer = Role::firstOrCreate(['name' => 'customer']);

        // 4. Assign Permissions

        // ADMIN: Gets access to everything
        $admin->syncPermissions([
            'view_users',
            'create_users',
            'edit_users',
        ]);

        // CS LEADER: Can view the list (but maybe not create/edit by default)
        // You can change this later in your "Feature Settings" page.
        $leader->syncPermissions(['view_users']);

        // CS STAFF: Starts with nothing (or add 'view_users' if you prefer)
        // $staff->syncPermissions(['view_users']);
    }
}
