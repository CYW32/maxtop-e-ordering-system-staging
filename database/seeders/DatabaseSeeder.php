<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    // database/seeders/DatabaseSeeder.php

    public function run(): void
    {
        $this->call(RoleSeeder::class); // Make sure RoleSeeder runs first!

        // Create the Admin User
        $user = \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'login_id' => 'admin001', // <--- YOU MUST ADD THIS LINE
        ]);

        // Assign the 'admin' role to this user so you can log in and test
        $user->assignRole('admin');
    }
}
