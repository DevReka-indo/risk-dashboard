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
        Schema::create('top_aturan_efektivitas', function (Blueprint $table): void {
            $table->id('id_aturan');
            $table->enum('kondisi_nilai', ['<', '=', '>']);
            $table->enum('kondisi_level', ['<', '=', '>']);
            $table->string('hasil', 30);
            $table->timestamps();

            $table->unique(
                ['kondisi_nilai', 'kondisi_level'],
                'top_aturan_efektivitas_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('top_aturan_efektivitas');
    }
};
