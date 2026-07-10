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
        // 1. Hapus total tabel lama jika ada agar bersih
        Schema::dropIfExists('smap_monitoring_periods');

        // 2. Buat tabel period baru untuk SMAP
        Schema::create('smap_monitoring_periods', function (Blueprint $table) {
            $table->id('id_detail'); // Primary Key tabel detail period
            $table->unsignedBigInteger('id_smap'); // Foreign Key ke master smap_monitoring

            // Inputan dinamis Triwulan & Tahun baru
            $table->string('quarter'); // Menyimpan 'TW1', 'TW2', 'TW3', 'TW4'
            $table->integer('year');   // Menyimpan angka tahun seperti 2026, 2027

            // Kolom parameter risiko spesifik per triwulan (Murni SMAP tanpa kolom Type)
            $table->integer('value');
            $table->integer('inherent');
            $table->unsignedBigInteger('id_level');
            $table->enum('trend', ['Naik', 'Turun', 'Stabil']);

            $table->timestamps();

            // Foreign Keys Relasi Baru SMAP
            $table->foreign('id_smap')->references('id_smap')->on('smap_monitoring')->onDelete('cascade');
            $table->foreign('id_level')->references('id_level')->on('level_risiko')->onDelete('cascade');

            // Unique Constraint Baru: 1 risiko SMAP tidak boleh ganda di kuartal + tahun yang sama
            $table->unique(['id_smap', 'quarter', 'year'], 'smap_risk_quarter_year_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smap_monitoring_periods');
    }
};
