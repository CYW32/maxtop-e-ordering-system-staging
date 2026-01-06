<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Ensure Roles and Permissions are created first [3, 4]
        $this->call(RoleSeeder::class);

        // 2. Create the Admin User (Developer Only) [1, 3]
        $admin = User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@test.com',
            'login_id' => 'admin001',
            'status' => 'active',
        ]);
        $admin->assignRole('admin');

        // 3. Create a CS Leader (Manages CS Staff and Customers) [1]
        $leader = User::factory()->create([
            'name' => 'CS Leader User',
            'email' => 'leader@test.com',
            'login_id' => 'csleader001',
            'status' => 'active',
        ]);
        $leader->assignRole('cs_leader');

        // 4. Create a CS Staff (Handles Items/Catalogs/Orders) [1]
        $staff = User::factory()->create([
            'name' => 'CS Staff User',
            'email' => 'staff@test.com',
            'login_id' => 'csstaff001',
            'status' => 'active',
        ]);
        $staff->assignRole('cs_staff');

        // 5. Create a Main Customer (Total Store / HQ) [1, 2]
        $mainCustomer = User::factory()->create([
            'name' => 'HQ Customer Corp',
            'email' => 'hq@customer.com',
            'login_id' => 'customer001',
            'status' => 'active',
            'assigned_cs_id' => $staff->id,
        ]);
        $mainCustomer->assignRole('customer');

        // Attach details to the HQ
        $mainCustomer->details()->create([
            'company_name' => 'HQ Customer Corp Sdn Bhd',
            'company_reg_no' => '202301012345',
            'pic_name' => 'Mr. Tan',
            'pic_phone' => '012-3456789',
            'delivery_address' => '123 Main St, HQ Office',
        ]);

        // 6. Create a Branch Customer (Linked to HQ) [1, 2]
        $branchCustomer = User::factory()->create([
            'name' => 'Branch Store Alpha',
            'email' => 'branch@customer.com',
            'login_id' => 'branch001',
            'status' => 'active',
            'parent_id' => $mainCustomer->id, // Branch Logic: Linked via parent_id [2, 5]
            'assigned_cs_id' => $staff->id,
        ]);
        $branchCustomer->assignRole('customer');

        // Attach details to the Branch
        $branchCustomer->details()->create([
            'company_name' => 'HQ Customer Corp (Branch Alpha)',
            'company_reg_no' => '202301012345', // Usually same as HQ
            'pic_name' => 'Ms. Siti',
            'pic_phone' => '012-9876543',
            'delivery_address' => '456 Side St, Branch Alpha',
        ]);
    }
}
