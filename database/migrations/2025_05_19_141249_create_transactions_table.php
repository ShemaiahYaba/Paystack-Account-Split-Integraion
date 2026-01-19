<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->morphs('transactionable');
            $table->decimal('amount', 15, 2);
            $table->string('reference')->unique();
            $table->string('status')->default('pending'); // pending, success, failed, refunded, etc.
            $table->string('currency', 3)->default('NGN');
            $table->string('channel')->nullable(); // e.g., card, bank_transfer
            $table->string('provider')->nullable(); // e.g., paystack, flutterwave
            $table->string('description');
            // Add to transactions table migration
            $table->string('subaccount_code')->nullable(); // Track which subaccount was used
            $table->string('user_state')->nullable(); // Snapshot of user's state at payment time
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
