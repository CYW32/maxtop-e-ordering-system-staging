<?php

namespace Database\Seeders;

use App\Models\Catalog;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- CONTROL KNOBS START ---
        $totalItems = 60;  // Total items in the global master list
        $totalCategories = 6;   // Sidebar categories for filtering
        $totalCatalogs = 3;   // Whitelist folders for different customers
        $branchesPerHQ = 4;   // Sub-accounts per Main HQ
        // --- CONTROL KNOBS END ---

        // 1. Initialize Roles and Permissions [4]
        // CRITICAL: Must be first so users can be assigned roles
        $this->call(RoleSeeder::class);

        // 2. Generate Product Items [5]
        $items = Item::factory()->count($totalItems)->create();

        // 3. Generate Categories & Link Items [6]
        // Fulfills Item Grouping Management
        $categories = Category::factory()->count($totalCategories)->create()->each(function ($category) use ($items) {
            $category->items()->attach(
                $items->random(rand(5, 12))->pluck('id')->toArray()
            );
        });

        // 4. Generate Catalogs & Whitelist Items [7]
        // Fulfills Section 3.a.3: Customers only see whitelisted items
        $catalogs = Catalog::factory()->count($totalCatalogs)->create()->each(function ($catalog) use ($items) {
            $catalog->items()->attach(
                $items->random(rand(15, 30))->pluck('id')->toArray()
            );
        });

        // 5. Create Internal Staff [8, 9]
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

        // Create a CS Staff (Handles Items/Catalogs/Orders) [1]
        $staff = User::factory()->create([
            'name' => 'CS Staff User',
            'email' => 'staff@test.com',
            'login_id' => 'csstaff001',
            'status' => 'active',
        ]);
        $staff->assignRole('cs_staff');

        // 6. Create Customer Hierarchy [1, 2]
        // Create Main HQ and assign to the first available Catalog
        $hq = User::factory()->create([
            'name' => 'Main HQ Corp',
            'login_id' => 'customer001',
            'email' => 'hq@customer.com',
            'catalog_id' => $catalogs->first()->id, // Assigned to Folder 1
            'assigned_cs_id' => $staff->id,
            'status' => 'active',
        ]);
        $hq->assignRole('customer');
        $hq->details()->create([
            'company_name' => 'Maxtop HQ Group',
            'delivery_address' => '123 Industrial Park, KL',
        ]);

        // Create Branches using the control knob [Section 3.a.2 Inheritance]
        User::factory()->count($branchesPerHQ)->create([
            'parent_id' => $hq->id,
            'catalog_id' => null, // NULL forces inheritance from HQ [10]
            'assigned_cs_id' => $staff->id,
            'status' => 'active',
        ])->each(function ($branch) {
            $branch->assignRole('customer');
            $branch->details()->create([
                'company_name' => $branch->name.' Branch',
                'delivery_address' => 'Branch Location Address',
            ]);
        });
    }
}
