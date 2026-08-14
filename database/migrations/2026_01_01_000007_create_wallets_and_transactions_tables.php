<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('balance', 12, 2)->default(0.00);
            $table->decimal('escrow_balance', 12, 2)->default(0.00);
            $table->string('currency')->default('USD');
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('wallets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type'); // deposit, escrow_lock, escrow_release, platform_fee, payout, refund
            $table->decimal('amount', 12, 2);
            $table->decimal('fee', 10, 2)->default(0.00);
            $table->string('reference_type')->nullable(); // Contract, Milestone, PayoutRequest, Job
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('description');
            $table->string('status')->default('completed'); // pending, completed, failed, cancelled
            $table->timestamps();
        });

        Schema::create('payout_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('payout_method')->default('bank_transfer'); // bank_transfer, paypal, stripe_connect, crypto
            $table->json('account_details')->nullable();
            $table->string('status')->default('pending'); // pending, processed, rejected
            $table->text('admin_notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_requests');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('wallets');
    }
};
