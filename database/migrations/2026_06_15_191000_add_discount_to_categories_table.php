<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'discount_type')) {
                $table->string('discount_type')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('categories', 'discount_value')) {
                $table->decimal('discount_value', 10, 2)->nullable()->after('discount_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value']);
        });
    }
};
