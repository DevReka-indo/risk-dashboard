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
        Schema::create('top_level_risiko', function (Blueprint $table): void {
            $table->id('id_level');
            $table->string('nama_level', 30);
            $table->unsignedTinyInteger('urutan');
            $table->string('kode_warna', 7);
            $table->timestamps();

            $table->unique('nama_level');
            $table->unique('urutan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('top_level_risiko');
    }
};
