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
        Schema::create('print_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');                                          // Label bebas, misal "Printer Kasir Utama"
            $table->string('ip_address');                                    // IP printer di jaringan LAN
            $table->unsignedSmallInteger('port')->default(9100);             // Default 9100 (RAW/ESC-POS) atau 631 (IPP)
            $table->enum('protocol', ['raw', 'ipp'])->default('raw');        // Protokol koneksi
            $table->enum('paper_size', ['58mm', '80mm', 'a4'])->default('80mm'); // Ukuran kertas
            $table->boolean('is_default')->default(false);                   // Hanya 1 yang bisa default
            $table->boolean('is_active')->default(true);                     // Status aktif
            $table->text('notes')->nullable();                               // Catatan tambahan
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('print_settings');
    }
};
