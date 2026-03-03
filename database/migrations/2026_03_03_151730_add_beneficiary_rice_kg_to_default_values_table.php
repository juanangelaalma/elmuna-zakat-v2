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
        Schema::table('default_values', function (Blueprint $table) {
            $table->decimal('beneficiary_rice_kg')->nullable()->default(5)->after('unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('default_values', function (Blueprint $table) {
            $table->dropColumn('beneficiary_rice_kg');
        });
    }
};
