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
            $table->timestamp('email_changed_at')->nullable()->after('email_verified_at');
            $table->timestamp('email_confirmation_sent_at')->nullable()->after('email_changed_at');
            $table->string('email_confirmation_token')->nullable()->after('email_confirmation_sent_at');
            $table->string('pending_email')->nullable()->after('email_confirmation_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email_changed_at', 'email_confirmation_sent_at', 'email_confirmation_token', 'pending_email']);
        });
    }
};
