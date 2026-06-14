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
        Schema::dropIfExists('location_rates');
        Schema::create('location_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained()->cascadeOnDelete();
            $table->string('area');
            $table->decimal('charge', 10, 2)->default(60.00);
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_rates');
        Schema::create('location_rates', function (Blueprint $table) {
            $table->id();
            $table->string('country');
            $table->string('district');
            $table->string('area');
            $table->decimal('charge', 10, 2)->default(60.00);
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }
};
