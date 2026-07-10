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
        // 1. Tambah kolom di tabel riwayat triwulan (pivot)
        Schema::table('dep_monitoring_periods', function (Blueprint $table) {
            $table->enum('penanganan', ['Belum', 'Proses', 'Sudah'])->default('Belum')->after('trend');
            $table->integer('target_value')->nullable()->after('penanganan');
            $table->unsignedBigInteger('target_id_level')->nullable()->after('target_value');
        });

        // 2. Tambah kolom di tabel utama (untuk sinkronisasi status terbaru)
        Schema::table('dep_monitoring', function (Blueprint $table) {
            $table->enum('penanganan', ['Belum', 'Proses', 'Sudah'])->default('Belum')->after('trend');
            $table->integer('target_value')->nullable()->after('penanganan');
            $table->unsignedBigInteger('target_id_level')->nullable()->after('target_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dep_monitoring_periods', function (Blueprint $table) {
            $table->dropColumn(['penanganan', 'target_value', 'target_id_level']);
        });

        Schema::table('dep_monitoring', function (Blueprint $table) {
            $table->dropColumn(['penanganan', 'target_value', 'target_id_level']);
        });
    }
};
