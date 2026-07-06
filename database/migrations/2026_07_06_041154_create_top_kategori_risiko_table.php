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
        Schema::create('top_kategori_risiko', function (Blueprint $table): void {
            $table->id('id_kategori');
            $table->string('nama_kategori', 100);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique('nama_kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('top_kategori_risiko');
    }
};
