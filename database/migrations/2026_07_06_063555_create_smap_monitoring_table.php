<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('smap_monitoring', function (Blueprint $table) {
            $table->id('id_smap');

            // Foreign Key ke top_unit_kerja
            $table->foreignId('id_unit')->constrained('top_unit_kerja', 'id_unit')->onDelete('cascade');

            // PERBAIKAN: Diarahkan ke top_kategori_risiko dan id_kategori
            $table->foreignId('id_kategori')->constrained('top_kategori_risiko', 'id_kategori')->onDelete('cascade');

            // PERBAIKAN: Diarahkan ke top_level_risiko dan id_level
            $table->foreignId('id_level')->constrained('top_level_risiko', 'id_level')->onDelete('cascade');

            $table->text('risk_event_deta');
            $table->integer('value');
            $table->integer('inherent');
            $table->enum('trend', ['Naik', 'Turun', 'Stabil']);
            $table->boolean('status')->default(true);
            $table->timestamps(); // Menggunakan timestamps() standar agar konsisten dengan tabel lainnya
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smap_monitoring');
    }
};
