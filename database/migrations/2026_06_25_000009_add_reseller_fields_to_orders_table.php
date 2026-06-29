<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('reseller_profile_id')->nullable()->constrained()->nullOnDelete()->after('user_id');
            $table->decimal('reseller_discount_amount', 10, 2)->default(0)->after('shipping_charge');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['reseller_profile_id']);
            $table->dropColumn(['reseller_profile_id', 'reseller_discount_amount']);
        });
    }
};
