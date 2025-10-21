<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full system access with all permissions',
                'permissions' => json_encode([
                    'manage_users' => true,
                    'manage_domains' => true,
                    'manage_orders' => true,
                    'manage_offers' => true,
                    'manage_messages' => true,
                    'view_analytics' => true,
                    'manage_settings' => true,
                    'resolve_disputes' => true,
                    'manage_commissions' => true,
                    'access_admin_panel' => true
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'moderator',
                'display_name' => 'Moderator',
                'description' => 'Limited admin access for content moderation',
                'permissions' => json_encode([
                    'manage_domains' => true,
                    'manage_orders' => true,
                    'manage_offers' => true,
                    'manage_messages' => true,
                    'view_analytics' => true,
                    'resolve_disputes' => true,
                    'access_admin_panel' => true
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'user',
                'display_name' => 'Regular User',
                'description' => 'Standard user with buying and selling capabilities',
                'permissions' => json_encode([
                    'list_domains' => true,
                    'buy_domains' => true,
                    'make_offers' => true,
                    'send_messages' => true,
                    'manage_favorites' => true,
                    'view_own_analytics' => true
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'verified_seller',
                'display_name' => 'Verified Seller',
                'description' => 'Verified seller with enhanced selling capabilities',
                'permissions' => json_encode([
                    'list_domains' => true,
                    'buy_domains' => true,
                    'make_offers' => true,
                    'send_messages' => true,
                    'manage_favorites' => true,
                    'view_own_analytics' => true,
                    'featured_listings' => true,
                    'bulk_operations' => true
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($roles as $role) {
            DB::table('user_roles')->updateOrInsert(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
