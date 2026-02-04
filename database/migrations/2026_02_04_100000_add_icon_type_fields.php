<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->enum('icon_type', ['emoji', 'font-awesome', 'image'])->default('emoji')->after('icon');
            $table->string('icon_url')->nullable()->after('icon_type');
        });

        Schema::table('links', function (Blueprint $table) {
            $table->enum('icon_type', ['emoji', 'font-awesome', 'image'])->default('emoji')->after('icon');
            $table->string('icon_url')->nullable()->after('icon_type');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['icon_type', 'icon_url']);
        });

        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn(['icon_type', 'icon_url']);
        });
    }
};
