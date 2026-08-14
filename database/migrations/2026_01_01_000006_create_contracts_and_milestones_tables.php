<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_posting_id')->constrained('job_postings')->cascadeOnDelete();
            $table->foreignId('proposal_id')->nullable()->constrained('proposals')->nullOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('freelancer_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('type')->default('fixed_price'); // fixed_price, hourly
            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('platform_fee_percent', 5, 2)->default(10.00);
            $table->string('status')->default('active'); // active, completed, cancelled, disputed, paused
            $table->text('terms')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamps();
        });

        Schema::create('contract_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('due_date')->nullable();
            $table->string('status')->default('pending'); // pending, funded_in_escrow, submitted_for_approval, approved_and_released, cancelled
            $table->text('submission_notes')->nullable();
            $table->json('submission_attachments')->nullable();
            $table->timestamp('funded_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_milestones');
        Schema::dropIfExists('contracts');
    }
};
