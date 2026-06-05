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
        Schema::table('webauthn_credentials', function (Blueprint $table) {
            // Windows Hello 等 platform authenticator 的 credential ID
            // 可能超过 255 字节，base64 编码后超过 255 字符
            $table->text('credential_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('webauthn_credentials', function (Blueprint $table) {
            $table->string('credential_id', 255)->change();
        });
    }
};
