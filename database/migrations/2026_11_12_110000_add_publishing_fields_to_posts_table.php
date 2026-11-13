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
        Schema::table('posts', function (Blueprint $table) {
            $table->enum('status', ['draft', 'published'])->default('published')->after('body');
            $table->boolean('is_pinned')->default(false)->after('status');
            $table->unsignedInteger('views')->default(0)->after('is_pinned');
            $table->index(['status', 'is_pinned']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['status', 'is_pinned']);
            $table->dropColumn(['status', 'is_pinned', 'views']);
        });
    }
};
