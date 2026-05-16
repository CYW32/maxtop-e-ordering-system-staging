<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

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

        // NEW: Granular visibility permissions for the unified user management dashboard
        Permission::firstOrCreate(['name' => 'view_all_users']);
        Permission::firstOrCreate(['name' => 'view_my_users']);

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        $leader = Role::firstOrCreate(['name' => 'cs_leader']);
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
            'view_all_users', 
        ]);

        $staff = Role::firstOrCreate(['name' => 'cs_staff']);
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
            'view_my_users', 
        ]);

        $customer = Role::firstOrCreate(['name' => 'customer']);
    }
}