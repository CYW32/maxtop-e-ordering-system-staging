<?php

namespace Database\Seeders;

use App\Models\Catalog;
use App\Models\Company;
use App\Models\Item;
use App\Models\Uom; 
use App\Models\User; 
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        // 1. Create Realistic Items with specific UOMs (替换掉原本的 random factory)
        $realItemsData = [
            [
                'sku' => 'MT-TRAY-SEALER',
                'name' => 'TRAY SEALER',
                'description' => 'Packaging tray sealer machine.',
                'uoms' => [
                    ['uom_name' => 'Machine', 'rate_qty' => 1, 'price' => 1500.00], // 机器通常按单台卖
                ]
            ],
            [
                'sku' => 'MT-PP-TRAY',
                'name' => 'SEALABLE PP PLASTIC TRAY',
                'description' => 'Sealable PP plastic tray for food packaging.',
                'uoms' => [
                    ['uom_name' => 'Carton 500s', 'rate_qty' => 500, 'price' => 120.00], // 假设一箱500个
                    ['uom_name' => 'Pack 50s', 'rate_qty' => 50, 'price' => 15.00],      // 假设一包50个
                ]
            ],
            [
                'sku' => 'MT-PAPER-TRAY',
                'name' => 'PAPER TRAY',
                'description' => 'Kraft paper tray available in various sizes.',
                'uoms' => [
                    ['uom_name' => 'Carton 1000s', 'rate_qty' => 1000, 'price' => 85.00],
                    ['uom_name' => 'Pack 100s', 'rate_qty' => 100, 'price' => 10.00],
                ]
            ],
            [
                'sku' => 'MT-PAPER-LID',
                'name' => 'CENTRE HOLE PAPER LID (80mm / 90mm)',
                'description' => 'Centre hole paper lid suitable for cups.',
                'uoms' => [
                    ['uom_name' => 'Carton 1000s', 'rate_qty' => 1000, 'price' => 60.00],
                    ['uom_name' => 'Sleeve 50s', 'rate_qty' => 50, 'price' => 4.00],
                ]
            ]
        ];

        // 用一个集合来收集创建好的真实商品，方便下面 attach 给 Catalog
        $items = collect();

        foreach ($realItemsData as $data) {
            $item = Item::create([
                'sku' => $data['sku'],
                'name' => $data['name'],
                'description' => $data['description'],
                'status' => 'active',
            ]);

            foreach ($data['uoms'] as $uom) {
                Uom::create([
                    'item_id' => $item->id,
                    'uom_name' => $uom['uom_name'],
                    'rate_qty' => $uom['rate_qty'],
                    'price' => $uom['price'],
                    'status' => 'active',
                ]);
            }

            $items->push($item);
        }

        // 2. Create Catalogs and attach items
        $catalogs = Catalog::factory()->count(3)->create()->each(function ($catalog) use ($items) {
            // 把刚才创建的所有真实商品都挂载到每个 Catalog 下，这样每个客户都能看到
            $catalog->items()->attach($items->pluck('id')->toArray());
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