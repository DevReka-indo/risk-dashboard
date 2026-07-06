<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vdpt_monitoring', function (Blueprint $table) {
            $table->id('id_monitoring');

            // Foreign Keys yang diarahkan ke top_unit_kerja sesuai gambar image_b680bb.png
            $table->foreignId('id_unit')->constrained('top_unit_kerja', 'id_unit')->onDelete('cascade');

            // Foreign Keys Lainnya
            $table->foreignId('id_category')->constrained('vds_categorie', 'id_category')->onDelete('cascade');
            $table->foreignId('id_level')->constrained('vds_level', 'id_level')->onDelete('cascade');

            // Kolom Lainnya
            $table->text('risk_event_deta');
            $table->integer('value');
            $table->integer('inherent');
            $table->enum('trend', ['Naik', 'Turun', 'Stabil']);
            $table->boolean('status')->default(true);
            $table->enum('type', ['Proyek', 'Non-Proyek']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vdpt_monitoring');
    }
};
