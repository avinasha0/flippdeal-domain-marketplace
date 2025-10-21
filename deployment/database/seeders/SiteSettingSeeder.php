<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SiteSetting;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'site_name',
                'value' => 'Domain Marketplace',
                'type' => 'string',
                'description' => 'The name of the website',
                'group' => 'general',
                'is_public' => true
            ],
            [
                'key' => 'site_description',
                'value' => 'Buy and sell premium domain names with confidence',
                'type' => 'string',
                'description' => 'Site description for SEO',
                'group' => 'general',
                'is_public' => true
            ],
            [
                'key' => 'site_logo',
                'value' => '/images/logo.png',
                'type' => 'string',
                'description' => 'Path to the site logo',
                'group' => 'general',
                'is_public' => true
            ],
            [
                'key' => 'contact_email',
                'value' => 'support@domainmarketplace.com',
                'type' => 'string',
                'description' => 'Contact email address',
                'group' => 'general',
                'is_public' => true
            ],
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable maintenance mode',
                'group' => 'general',
                'is_public' => false
            ],

            // Payment Settings
            [
                'key' => 'default_commission_rate',
                'value' => '5.00',
                'type' => 'float',
                'description' => 'Default commission rate percentage',
                'group' => 'payment',
                'is_public' => false
            ],
            [
                'key' => 'paypal_client_id',
                'value' => '',
                'type' => 'string',
                'description' => 'PayPal client ID for payments',
                'group' => 'payment',
                'is_public' => false
            ],
            [
                'key' => 'paypal_client_secret',
                'value' => '',
                'type' => 'string',
                'description' => 'PayPal client secret',
                'group' => 'payment',
                'is_public' => false
            ],
            [
                'key' => 'paypal_mode',
                'value' => 'sandbox',
                'type' => 'string',
                'description' => 'PayPal mode (sandbox or live)',
                'group' => 'payment',
                'is_public' => false
            ],
            [
                'key' => 'minimum_withdrawal',
                'value' => '50.00',
                'type' => 'float',
                'description' => 'Minimum withdrawal amount',
                'group' => 'payment',
                'is_public' => true
            ],

            // Email Settings
            [
                'key' => 'smtp_host',
                'value' => 'smtp.gmail.com',
                'type' => 'string',
                'description' => 'SMTP host for email',
                'group' => 'email',
                'is_public' => false
            ],
            [
                'key' => 'smtp_port',
                'value' => '587',
                'type' => 'integer',
                'description' => 'SMTP port',
                'group' => 'email',
                'is_public' => false
            ],
            [
                'key' => 'smtp_username',
                'value' => '',
                'type' => 'string',
                'description' => 'SMTP username',
                'group' => 'email',
                'is_public' => false
            ],
            [
                'key' => 'smtp_password',
                'value' => '',
                'type' => 'string',
                'description' => 'SMTP password',
                'group' => 'email',
                'is_public' => false
            ],
            [
                'key' => 'smtp_encryption',
                'value' => 'tls',
                'type' => 'string',
                'description' => 'SMTP encryption type',
                'group' => 'email',
                'is_public' => false
            ],

            // Verification Settings
            [
                'key' => 'require_paypal_verification',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Require PayPal email verification',
                'group' => 'verification',
                'is_public' => false
            ],
            [
                'key' => 'require_government_id',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Require government ID verification',
                'group' => 'verification',
                'is_public' => false
            ],
            [
                'key' => 'verification_expiry_days',
                'value' => '7',
                'type' => 'integer',
                'description' => 'Days until verification expires',
                'group' => 'verification',
                'is_public' => false
            ],

            // Domain Settings
            [
                'key' => 'auto_approve_domains',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Auto-approve domain listings',
                'group' => 'domains',
                'is_public' => false
            ],
            [
                'key' => 'require_domain_verification',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Require domain ownership verification',
                'group' => 'domains',
                'is_public' => false
            ],
            [
                'key' => 'max_domains_per_user',
                'value' => '100',
                'type' => 'integer',
                'description' => 'Maximum domains per user',
                'group' => 'domains',
                'is_public' => false
            ],

            // Auction Settings
            [
                'key' => 'default_auction_duration_hours',
                'value' => '72',
                'type' => 'integer',
                'description' => 'Default auction duration in hours',
                'group' => 'auction',
                'is_public' => true
            ],
            [
                'key' => 'minimum_bid_increment',
                'value' => '10',
                'type' => 'integer',
                'description' => 'Minimum bid increment in dollars',
                'group' => 'auction',
                'is_public' => true
            ],
            [
                'key' => 'auto_extend_minutes',
                'value' => '5',
                'type' => 'integer',
                'description' => 'Minutes to extend auction if bid near end',
                'group' => 'auction',
                'is_public' => true
            ]
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}