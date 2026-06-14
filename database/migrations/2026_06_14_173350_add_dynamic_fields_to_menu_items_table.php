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
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('type')->default('custom')->after('title');
            $table->foreignId('category_id')->nullable()->after('type')->constrained()->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->after('category_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['brand_id']);
            $table->dropColumn(['type', 'category_id', 'brand_id']);
        });
    }
};
