<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
            $table->unsignedInteger('page_view_count')->default(0)->after('click_count');
        });
    }

    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn(['slug', 'page_view_count']);
        });
    }
};
