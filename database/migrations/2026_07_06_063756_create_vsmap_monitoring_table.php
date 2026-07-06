<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vsmap_monitoring', function (Blueprint $table) {
            $table->id('id_smap');

            // FOREIGN KEYS
            // Hubungan ke tabel top_unit_kerja (Menggantikan master department lama)
            $table->foreignId('id_unit')->constrained('top_unit_kerja', 'id_unit')->onDelete('cascade');

            // Hubungan ke tabel master categories dan levels
            $table->foreignId('id_category')->constrained('vds_categorie', 'id_category')->onDelete('cascade');
            $table->foreignId('id_level')->constrained('vds_level', 'id_level')->onDelete('cascade');

            // KOLOM DATA UTAMA SMAP
            $table->text('risk_event');
            $table->integer('value');
            $table->integer('inherent');
            $table->enum('trend', ['naik', 'stagnan', 'turun']);
            $table->enum('status', ['aktif', 'nonaktif']);

            // Timestamp pencatatan data sesuai dengan diagram SMAP terbaru
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vsmap_monitoring');
    }
};
