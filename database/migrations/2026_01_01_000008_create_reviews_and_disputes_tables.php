<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewee_id')->constrained('users')->cascadeOnDelete();
            $table->string('role'); // client_to_freelancer, freelancer_to_client
            $table->decimal('rating', 3, 2); // e.g. 5.00
            $table->decimal('communication_rating', 3, 2)->default(5.00);
            $table->decimal('quality_rating', 3, 2)->default(5.00);
            $table->decimal('deadline_rating', 3, 2)->default(5.00);
            $table->text('feedback');
            $table->timestamps();
        });

        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->cascadeOnDelete();
            $table->foreignId('raised_by')->constrained('users')->cascadeOnDelete();
            $table->string('reason');
            $table->text('description');
            $table->json('evidence')->nullable();
            $table->string('status')->default('opened'); // opened, under_review, resolved, closed
            $table->text('resolution_note')->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disputes');
        Schema::dropIfExists('reviews');
    }
};
