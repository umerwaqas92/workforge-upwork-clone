<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            if (!Schema::hasColumn('job_postings', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('status');
            }
            if (!Schema::hasColumn('job_postings', 'featured_until')) {
                $table->timestamp('featured_until')->nullable()->after('is_featured');
            }
        });

        Schema::table('proposals', function (Blueprint $table) {
            if (!Schema::hasColumn('proposals', 'is_boosted')) {
                $table->boolean('is_boosted')->default(false)->after('status');
            }
            if (!Schema::hasColumn('proposals', 'boosted_connects')) {
                $table->integer('boosted_connects')->default(0)->after('is_boosted');
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'featured_until']);
        });

        Schema::table('proposals', function (Blueprint $table) {
            $table->dropColumn(['is_boosted', 'boosted_connects']);
        });
    }
};
