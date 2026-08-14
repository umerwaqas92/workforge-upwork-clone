<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_posting_id')->constrained('job_postings')->cascadeOnDelete();
            $table->foreignId('freelancer_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('bid_amount', 10, 2);
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->decimal('platform_fee', 10, 2)->default(0);
            $table->decimal('receive_amount', 10, 2)->default(0);
            $table->integer('delivery_time_days')->nullable();
            $table->longText('cover_letter');
            $table->json('milestones')->nullable();
            $table->json('attachments')->nullable();
            $table->string('status')->default('pending'); // pending, shortlisted, accepted, rejected, withdrawn
            $table->boolean('client_seen')->default(false);
            $table->timestamps();

            $table->unique(['job_posting_id', 'freelancer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
