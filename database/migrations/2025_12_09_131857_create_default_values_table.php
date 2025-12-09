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
        Schema::create('default_values', function (Blueprint $table) {
            $table->id();
            $table->decimal('rice_sales_quantity')->nullable();
            $table->decimal('rice_sales_amount')->nullable();
            $table->decimal('rice_quantity')->nullable();
            $table->decimal('fidyah_quantity')->nullable();
            $table->decimal('fidyah_amount')->nullable();
            $table->string('unit', 4)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('default_values');
    }
};
