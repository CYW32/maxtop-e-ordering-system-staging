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
        Permission::firstOrCreate(['name' => 'view_business_entities']);
        Permission::firstOrCreate(['name' => 'view_login_credentials']);
        Permission::firstOrCreate(['name' => 'create_users']);
        Permission::firstOrCreate(['name' => 'edit_users']);
        Permission::firstOrCreate(['name' => 'view_assigned_customers']);
        Permission::firstOrCreate(['name' => 'edit_assigned_customers']);
        Permission::firstOrCreate(['name' => 'reassign_customers']);
        Permission::firstOrCreate(['name' => 'view_catalogs']);
        Permission::firstOrCreate(['name' => 'create_catalogs']);
        Permission::firstOrCreate(['name' => 'edit_catalogs']);
        Permission::firstOrCreate(['name' => 'view_items']);
        Permission::firstOrCreate(['name' => 'create_items']);
        Permission::firstOrCreate(['name' => 'edit_items']);

        // 3. Create Roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());
        $leader = Role::firstOrCreate(['name' => 'cs_leader']);
        $staff = Role::firstOrCreate(['name' => 'cs_staff']);
        $customer = Role::firstOrCreate(['name' => 'customer']);

        // CS LEADER: Can view the list (but maybe not create/edit by default)
        // You can change this later in your "Feature Settings" page.
        $leader->syncPermissions([
            'view_business_entities',
            'view_login_credentials',
            'edit_users',
            'view_assigned_customers',
            'edit_assigned_customers',
            'reassign_customers',
            'view_catalogs',
            'create_catalogs',
            'edit_catalogs',
            'view_items',
            'create_items',
            'edit_items',
        ]);

        // CS STAFF: Starts with nothing (or add 'view_users' if you prefer)
        // $staff->syncPermissions(['view_users']);
        $staff->syncPermissions([
            'view_business_entities',
            'view_login_credentials',
            'view_assigned_customers',
            'edit_assigned_customers',
            'view_catalogs',
            'create_catalogs',
            'edit_catalogs',
            'view_items',
            'create_items',
            'edit_items',
        ]);
    }
}
