<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->bigInteger('balance')->default(0)->comment('Balance in cents/paise');
            $table->string('currency')->default('INR');
            $table->timestamps();
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // credit, debit
            $table->bigInteger('amount'); // absolute amount in cents/paise
            $table->decimal('opening_balance', 16, 2)->nullable(); // Balance before transaction
            $table->decimal('closing_balance', 16, 2)->nullable(); // Balance after transaction
            $table->string('reference_id')->nullable(); // Order ID, Payment ID
            $table->string('description')->nullable();
            $table->string('status')->default('pending'); // pending, success, failed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
    }
};
