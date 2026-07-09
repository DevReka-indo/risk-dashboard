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
    // 1. Hapus total tabel lama agar bersih
    Schema::dropIfExists('dep_monitoring_periods');

    // 2. Buat ulang tabel dengan struktur parameter baru (Tanpa ->after())
    Schema::create('dep_monitoring_periods', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('id_monitoring');

        // Inputan dinamis Triwulan & Tahun baru
        $table->string('quarter'); // Menyimpan 'TW1', 'TW2', dll
        $table->integer('year');   // Menyimpan angka tahun seperti 2026, 2027

        // Kolom parameter risiko spesifik per triwulan
        $table->integer('value');
        $table->integer('inherent');
        $table->unsignedBigInteger('id_level');
        $table->enum('trend', ['Naik', 'Turun', 'Stabil']);

        $table->timestamps();

        // Foreign Keys Relasi Baru
        $table->foreign('id_monitoring')->references('id_monitoring')->on('dep_monitoring')->onDelete('cascade');
        $table->foreign('id_level')->references('id_level')->on('level_risiko')->onDelete('cascade');

        // Unique Constraint Baru: 1 risiko tidak boleh memiliki data ganda di kuartal + tahun yang sama
        $table->unique(['id_monitoring', 'quarter', 'year'], 'dep_risk_quarter_year_unique');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dep_monitoring_periods', function (Blueprint $table) {
            // Rollback constraints dan kolom
            $table->dropUnique('dep_risk_quarter_year_unique');
            $table->dropForeign(['id_level']);

            $table->dropColumn(['quarter', 'year', 'value', 'inherent', 'id_level', 'trend']);

            // Kembalikan kolom id_period jika diperlukan pasca rollback
            $table->unsignedBigInteger('id_period')->nullable()->after('id_monitoring');
        });
    }
};
