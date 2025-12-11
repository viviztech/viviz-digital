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
        Schema::table('products', function (Blueprint $table) {
            // Make it nullable first to allow migration of existing data if needed, 
            // though we are assuming fresh start or simple migration here.
            $table->foreignId('category_id')->nullable()->after('shop_id')->constrained()->nullOnDelete();

            // We can drop the type column later or keep it for now. 
            // Let's drop it to force the refactor completion.
            $table->dropColumn('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
            $table->enum('type', ['photo', 'video', 'audio', 'template', 'graphic'])->default('photo')->after('price');
        });
    }
};
