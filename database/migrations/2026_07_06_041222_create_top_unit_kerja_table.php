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
        Schema::create('top_unit_kerja', function (Blueprint $table): void {
            $table->id('id_unit');
            $table->string('nama_unit', 100);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique('nama_unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('top_unit_kerja');
    }
};
