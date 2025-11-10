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
        Schema::create('rices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_detail_id')->constrained('transaction_details')->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->string('unit_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rices'); // This will also drop related tables due to cascade on transaction_detail_id
    }
};
