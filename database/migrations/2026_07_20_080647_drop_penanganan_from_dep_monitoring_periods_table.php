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
        Schema::table('dep_monitoring_periods', function (Blueprint $table) {
            // Menghapus kolom penanganan dari tabel dep_monitoring_periods
            $table->dropColumn('penanganan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dep_monitoring_periods', function (Blueprint $table) {
            // Mengembalikan kolom jika migrasi di-rollback
            $table->enum('penanganan', ['Belum', 'Proses', 'Selesai'])->default('Belum');
        });
    }
};
