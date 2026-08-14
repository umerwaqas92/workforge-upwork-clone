<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('freelancer')->after('email'); // 'client', 'freelancer', 'admin'
            $table->string('avatar')->nullable()->after('role');
            $table->string('phone')->nullable()->after('avatar');
            $table->string('status')->default('active')->after('phone'); // 'active', 'suspended', 'pending'
            $table->string('country')->nullable()->after('status');
            $table->string('city')->nullable()->after('country');
            $table->string('timezone')->default('UTC')->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'avatar', 'phone', 'status', 'country', 'city', 'timezone']);
        });
    }
};
