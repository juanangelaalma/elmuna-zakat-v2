<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('shift_handover_id')
                ->nullable()
                ->constrained('shift_handovers')
                ->nullOnDelete()
                ->after('created_by');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['shift_handover_id']);
            $table->dropColumn('shift_handover_id');
        });
    }
};
