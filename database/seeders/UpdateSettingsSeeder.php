<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class UpdateSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General
            [
                'key' => 'site_name',
                'value' => 'AuraAssets',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Site Name',
                'description' => 'The name of the marketplace application.',
            ],
            [
                'key' => 'currency_code',
                'value' => 'USD',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Currency Code',
                'description' => 'The currency code for the marketplace (e.g. USD, INR).',
            ],
            [
                'key' => 'currency_symbol',
                'value' => '$',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Currency Symbol',
                'description' => 'The currency symbol to display (e.g. $, ₹).',
            ],
            // Social
            [
                'key' => 'social_facebook',
                'value' => 'https://facebook.com',
                'type' => 'string',
                'group' => 'social',
                'label' => 'Facebook URL',
                'description' => 'Link to the Facebook page.',
            ],
            [
                'key' => 'social_twitter',
                'value' => 'https://twitter.com',
                'type' => 'string',
                'group' => 'social',
                'label' => 'Twitter/X URL',
                'description' => 'Link to the Twitter/X profile.',
            ],
            [
                'key' => 'social_instagram',
                'value' => 'https://instagram.com',
                'type' => 'string',
                'group' => 'social',
                'label' => 'Instagram URL',
                'description' => 'Link to the Instagram profile.',
            ],
            [
                'key' => 'social_linkedin',
                'value' => 'https://linkedin.com',
                'type' => 'string',
                'group' => 'social',
                'label' => 'LinkedIn URL',
                'description' => 'Link to the LinkedIn profile.',
            ],
            // Payment
            [
                'key' => 'razorpay_key_id',
                'value' => '',
                'type' => 'string',
                'group' => 'payment',
                'label' => 'Razorpay Key ID',
                'description' => 'Your Razorpay Key ID.',
            ],
            [
                'key' => 'razorpay_key_secret',
                'value' => '',
                'type' => 'string', // Could be 'password' if we handle specific UI rendering
                'group' => 'payment',
                'label' => 'Razorpay Key Secret',
                'description' => 'Your Razorpay Key Secret.',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
