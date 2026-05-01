<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Run the RoleSeeder to create all roles and permissions
        $this->call(RoleSeeder::class);

        // 2. Create ONLY the System Admin user
        $admin = User::factory()->create([
            'name' => 'System Admin', 
            'login_id' => 'admin001', 
            'email' => 'admin@maxtop.com', 
            'status' => 'active'
        ]);
        
        // 3. Assign the admin role to the user
        $admin->assignRole('admin');
    }
}