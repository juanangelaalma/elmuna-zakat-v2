<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_handovers', function (Blueprint $table) {
            $table->id();
            $table->string('handing_over_officer_name');      // Petugas Pembuat/Penyerah
            $table->string('receiving_officer_name');          // Petugas Penerima
            $table->string('shift_name');                      // "Shift 1", "Shift 2", dll.
            $table->date('handover_date');

            // Kalkulasi total uang (Rp)
            $table->decimal('total_rice_sale_amount', 12, 2)->default(0);   // Penjualan Beras (Rp)
            $table->decimal('total_wealth_amount', 12, 2)->default(0);      // Zakat Maal (Rp)
            $table->decimal('total_fidyah_amount', 12, 2)->default(0);      // Fidyah (Rp)
            $table->decimal('total_donation_amount', 12, 2)->default(0);    // Shodaqoh (Rp)

            // Kalkulasi total beras (Kg)
            $table->decimal('total_rice_quantity', 10, 2)->default(0);      // Zakat Fitrah Bawa Beras (Kg)
            $table->decimal('total_fidyah_quantity', 10, 2)->default(0);    // Fidyah Beras (Kg)
            $table->decimal('total_donation_quantity', 10, 2)->default(0);  // Shodaqoh Beras (Kg)

            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_handovers');
    }
};
