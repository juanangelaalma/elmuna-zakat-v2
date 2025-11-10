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
        Schema::create('purchase_rice_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rice_sales_id')->constrained('rice_sales')->onDelete('cascade');
            $table->foreignId('purchase_rice_id')->constrained('purchase_rices')->onDelete('cascade');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_rice_allocations'); // This will also drop related tables due to cascade
    }
};
