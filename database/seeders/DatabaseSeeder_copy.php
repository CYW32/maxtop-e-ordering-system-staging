<?php

namespace Database\Seeders;

use App\Models\Catalog;
use App\Models\Company;
use App\Models\Item;
use App\Models\Uom; // Added for strict UOM generation
use App\Models\User; // NEW
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        // 1. Create Items with Mandatory UOMs [Addendum 5.a]
        $items = Item::factory()->count(60)->create()->each(function ($item) {
            // Fulfills Requirement: Random 1 to 5 UOMs
            $uomCount = rand(1, 5);
            $units = ['Box', 'Carton', 'Pack', 'Bundle', 'Pallet'];

            // 1. Mandatory Base Unit (Rate Qty 1) - Already correct in your code [3]
            Uom::create([
                'item_id' => $item->id,
                'uom_name' => 'Individual Unit',
                'rate_qty' => 1,
                'price' => rand(10, 100),
                'status' => 'active',
            ]);

            // 2. ARCHITECTURE FIX: Randomized Secondary Packaging Units [1]
            // Added 'price' field to satisfy NOT NULL constraint for Pure UOM model.
            for ($i = 1; $i < $uomCount; $i++) {
                Uom::create([
                    'item_id' => $item->id,
                    'uom_name' => $units[rand(0, 4)].' '.rand(10, 50).'s',
                    'rate_qty' => rand(5, 100),
                    'price' => rand(150, 1000), // FIXED: Mandatory price field
                    'status' => 'active',
                ]);
            }
        });

        // 2. Create Catalogs and attach items
        $catalogs = Catalog::factory()->count(3)->create()->each(function ($catalog) use ($items) {
            $catalog->items()->attach($items->random(rand(15, 30))->pluck('id')->toArray());
        });

        // 3. Create Internal Staff [Backbone 2.c]
        $admin = User::factory()->create(['name' => 'System Admin', 'login_id' => 'admin001', 'email' => 'admin@maxtop.com', 'status' => 'active']);
        $admin->assignRole('admin');

        $leader = User::factory()->create([
            'name' => 'CS Leader User',
            'email' => 'leader@test.com',
            'login_id' => 'csleader001',
            'status' => 'active',
        ]);
        $leader->assignRole('cs_leader');

        $staff = User::factory()->create(['name' => 'CS Staff User', 'login_id' => 'csstaff001', 'email' => 'staff@test.com', 'status' => 'active']);
        $staff->assignRole('cs_staff');

        // 4. Create Business Hierarchy [Addendum 1.a, 2.a]
        $hqCompany = Company::create([
            'company_code' => 'MAX-HQ-001',
            'company_name' => 'Maxtop HQ Group',
            'catalog_id' => $catalogs->first()->id,
            'delivery_address' => '123 Industrial Park, KL',
        ]);

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
