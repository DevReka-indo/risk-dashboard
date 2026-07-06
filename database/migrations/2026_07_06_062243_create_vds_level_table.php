<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vds_level', function (Blueprint $table) {
            $table->id('id_level');
            // Menyesuaikan dengan enum level (Contoh: Low, Low to Moderate, dsb)
            $table->enum('level_name', ['Low', 'Low to Moderate', 'Moderate', 'Moderate to High', 'High']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vds_level');
    }
};
