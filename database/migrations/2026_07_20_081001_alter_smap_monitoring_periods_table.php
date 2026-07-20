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
        Schema::table('smap_monitoring_periods', function (Blueprint $table) {
            // 1. Drop kolom enum lama (ganti 'nama_kolom_enum_lama' sesuai nama kolom asli di DB kamu)
            $table->dropColumn('status_penanganan');

            // 2. Tambahkan 3 kolom int baru
            $table->integer('progress_belum')->default(0)->after('efektif_risiko');
            $table->integer('progress_proses')->default(0)->after('progress_belum');
            $table->integer('progress_sudah')->default(0)->after('progress_proses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('smap_monitoring_periods', function (Blueprint $table) {
            // Rollback: Hapus 3 kolom baru
            $table->dropColumn(['progress_belum', 'progress_proses', 'progress_sudah']);

            // Rollback: Buat kembali kolom enum lama
            $table->string('status_penanganan')->nullable();
        });
    }
};
