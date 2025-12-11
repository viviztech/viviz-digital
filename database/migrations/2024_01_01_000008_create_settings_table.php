<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer, json
            $table->string('group')->default('general');
            $table->string('label');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed default settings
        DB::table('settings')->insert([
            ['key' => 'site_name', 'value' => 'AuraAssets', 'type' => 'string', 'group' => 'general', 'label' => 'Site Name', 'description' => 'The name of your marketplace', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_tagline', 'value' => 'Premium Digital Marketplace', 'type' => 'string', 'group' => 'general', 'label' => 'Tagline', 'description' => 'Short description shown in header', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'default_commission_rate', 'value' => '15', 'type' => 'integer', 'group' => 'payments', 'label' => 'Default Commission Rate (%)', 'description' => 'Platform fee percentage for new shops', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'min_payout_amount', 'value' => '5000', 'type' => 'integer', 'group' => 'payments', 'label' => 'Minimum Payout (cents)', 'description' => 'Minimum amount for vendor withdrawal', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'max_upload_size_mb', 'value' => '500', 'type' => 'integer', 'group' => 'uploads', 'label' => 'Max Upload Size (MB)', 'description' => 'Maximum file size for product uploads', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'download_expiry_hours', 'value' => '72', 'type' => 'integer', 'group' => 'downloads', 'label' => 'Download Link Expiry (hours)', 'description' => 'How long download links remain valid', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'max_downloads_per_order', 'value' => '5', 'type' => 'integer', 'group' => 'downloads', 'label' => 'Max Downloads Per Order', 'description' => 'Number of times a file can be downloaded', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'require_email_verification', 'value' => 'true', 'type' => 'boolean', 'group' => 'security', 'label' => 'Require Email Verification', 'description' => 'Users must verify email before purchasing', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'auto_approve_shops', 'value' => 'false', 'type' => 'boolean', 'group' => 'vendors', 'label' => 'Auto-Approve New Shops', 'description' => 'Automatically verify new vendor shops', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'featured_products_count', 'value' => '8', 'type' => 'integer', 'group' => 'homepage', 'label' => 'Featured Products Count', 'description' => 'Number of featured products on homepage', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'maintenance_mode', 'value' => 'false', 'type' => 'boolean', 'group' => 'general', 'label' => 'Maintenance Mode', 'description' => 'Put the site in maintenance mode', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'support_email', 'value' => 'support@auraassets.com', 'type' => 'string', 'group' => 'general', 'label' => 'Support Email', 'description' => 'Email for customer support inquiries', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
