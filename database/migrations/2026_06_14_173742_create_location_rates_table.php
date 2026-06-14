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
        Schema::create('location_rates', function (Blueprint $table) {
            $table->id();
            $table->string('country')->default('Bangladesh');
            $table->string('district');
            $table->string('area');
            $table->decimal('charge', 8, 2)->default(0.00);
            $table->string('status')->default('active');
            $table->unique(['country', 'district', 'area']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_rates');
    }
};
