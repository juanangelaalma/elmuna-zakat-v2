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
        Schema::create('purchase_rices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rice_item_id')->constrained('rice_items')->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->decimal('price_per_kg', 10, 2);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_rice');
    }
};
