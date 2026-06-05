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
            $table->boolean('two_factor_enabled')->default(false)->after('role');
            $table->text('totp_secret')->nullable()->after('two_factor_enabled');
            $table->text('totp_recovery_codes')->nullable()->after('totp_secret');
            $table->string('two_factor_remember_token', 100)->nullable()->unique()->after('totp_recovery_codes');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'two_factor_enabled',
                'totp_secret',
                'totp_recovery_codes',
                'two_factor_remember_token',
            ]);
        });
    }
};
