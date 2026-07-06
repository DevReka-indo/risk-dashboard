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
        Schema::create('top_risiko', function (Blueprint $table): void {
            $table->id('id_risiko');
            $table->string('nama_peristiwa_risiko', 255);

            $table->foreignId('id_kategori')
                ->constrained('top_kategori_risiko', 'id_kategori')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->date('tanggal_dibuat');
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            $table->index('id_kategori');
            $table->index('is_aktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('top_risiko');
    }
};
