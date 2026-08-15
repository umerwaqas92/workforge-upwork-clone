<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('freelancer_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('freelancer_profiles', 'badge_tier')) {
                $table->string('badge_tier', 50)->default('none')->after('is_top_rated');
            }
        });
    }

    public function down(): void
    {
        Schema::table('freelancer_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('freelancer_profiles', 'badge_tier')) {
                $table->dropColumn('badge_tier');
            }
        });
    }
};
