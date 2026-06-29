<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reseller_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->uuid('variant_id')->nullable();
            $table->decimal('custom_price', 10, 2);
            $table->timestamps();

            $table->unique(['reseller_profile_id', 'product_id', 'variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseller_prices');
    }
};
