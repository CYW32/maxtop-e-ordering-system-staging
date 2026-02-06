<?php

namespace Database\Seeders;

use App\Models\Catalog;
use App\Models\Company;
use App\Models\Item;
use App\Models\User; // NEW
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $items = Item::factory()->count(60)->create();
        $catalogs = Catalog::factory()->count(3)->create()->each(function ($catalog) use ($items) {
            $catalog->items()->attach($items->random(rand(15, 30))->pluck('id')->toArray());
        });

        $admin = User::factory()->create([
            'name' => 'System Admin',
            'login_id' => 'admin001',
            'email' => 'admin@maxtop.com',
            'status' => 'active',
        ]);
        $admin->assignRole('admin');

        // Create a CS Leader (Manages CS Staff and Customers) [1]
        $leader = User::factory()->create([
            'name' => 'CS Leader User',
            'email' => 'leader@test.com',
            'login_id' => 'csleader001',
            'status' => 'active',
        ]);
        $leader->assignRole('cs_leader');

        // 1. Create Internal Staff [Backbone 2.c]
        $staff = User::factory()->create([
            'name' => 'CS Staff User',
            'login_id' => 'csstaff001',
            'email' => 'staff@test.com',
            'status' => 'active',
        ]);
        $staff->assignRole('cs_staff');

        // 2. Create Main HQ Company [Addendum 1.a & 1.b]
        // Note: Catalog is now assigned to the COMPANY, not the User.
        $hqCompany = Company::create([
            'company_code' => 'MAX-HQ-001',
            'company_name' => 'Maxtop HQ Group',
            'catalog_id' => $catalogs->first()->id,
            'delivery_address' => '123 Industrial Park, KL',
        ]);

        // 3. Create Multiple HQ Users (Many-to-One) [Addendum 2.a]
        $hqUser = User::factory()->create([
            'name' => 'Main HQ Admin',
            'login_id' => 'customer001',
            'company_id' => $hqCompany->id, // Linked to Company entity
            'assigned_cs_id' => $staff->id,
            'status' => 'active',
        ]);
        $hqUser->assignRole('customer');

        // 4. Create Branch Companies and Users [Addendum 3.c]
        for ($i = 1; $i <= 4; $i++) {
            $branchCompany = Company::create([
                'parent_id' => $hqCompany->id, // Business hierarchy
                'branch_code' => "BR-00{$i}",
                'company_name' => "Maxtop Branch {$i}",
                'catalog_id' => null, // Forces inheritance from HQ Catalog [Backbone 3.a.2]
                'delivery_address' => "Branch {$i} Location Address",
            ]);

            $branchUser = User::factory()->create([
                'name' => "Branch User {$i}",
                'login_id' => "branch00{$i}",
                'company_id' => $branchCompany->id,
                'assigned_cs_id' => $staff->id,
                'status' => 'active',
            ]);
            $branchUser->assignRole('customer');
        }
    }
}
