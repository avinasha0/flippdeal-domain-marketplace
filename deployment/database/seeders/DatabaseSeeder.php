<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed user roles first
        $this->call([
            UserRoleSeeder::class,
            CategorySeeder::class,
            SiteSettingSeeder::class,
        ]);

        // Create default admin user
        User::updateOrCreate(
            ['email' => 'admin@flippdeal.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role_id' => 1, // Admin role
                'is_verified' => true,
                'email_verified_at' => now(),
                'account_status' => 'active',
                'paypal_verified' => true,
                'government_id_verified' => true,
            ]
        );

        // Create test user
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'role_id' => 3, // Regular user role
                'is_verified' => true,
                'email_verified_at' => now(),
                'account_status' => 'active',
                'paypal_verified' => true,
                'government_id_verified' => true,
            ]
        );
    }
}
