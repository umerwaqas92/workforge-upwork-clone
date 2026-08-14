<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('freelancer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('bio')->nullable();
            $table->decimal('hourly_rate', 10, 2)->default(25.00);
            $table->string('experience_level')->default('intermediate'); // entry, intermediate, expert
            $table->string('availability')->default('available_now'); // available_now, open_to_offers, not_available
            $table->string('english_level')->default('fluent'); // basic, conversational, fluent, native
            $table->integer('job_success_score')->default(100);
            $table->decimal('total_earnings', 12, 2)->default(0);
            $table->integer('completed_jobs_count')->default(0);
            $table->decimal('total_hours_worked', 10, 2)->default(0);
            $table->string('github_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->json('portfolio_items')->nullable();
            $table->json('certifications')->nullable();
            $table->json('education')->nullable();
            $table->boolean('is_top_rated')->default(false);
            $table->timestamps();
        });

        Schema::create('client_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('company_name')->nullable();
            $table->string('company_website')->nullable();
            $table->string('company_size')->nullable(); // '1-10', '11-50', '51-200', '201-500', '500+'
            $table->string('industry')->nullable();
            $table->string('tagline')->nullable();
            $table->text('about')->nullable();
            $table->boolean('payment_verified')->default(true);
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->integer('hires_count')->default(0);
            $table->integer('active_contracts_count')->default(0);
            $table->timestamps();
        });

        Schema::create('user_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->string('proficiency_level')->default('expert'); // beginner, intermediate, expert
            $table->timestamps();
            $table->unique(['user_id', 'skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_skills');
        Schema::dropIfExists('client_profiles');
        Schema::dropIfExists('freelancer_profiles');
    }
};
