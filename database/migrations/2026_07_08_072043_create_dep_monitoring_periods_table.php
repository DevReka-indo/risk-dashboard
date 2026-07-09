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
        Schema::create('dep_monitoring_periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_monitoring');
            $table->unsignedBigInteger('id_period');
            $table->timestamps();

            // Relasi Foreign Key
            $table->foreign('id_monitoring')->references('id_monitoring')->on('dep_monitoring')->onDelete('cascade');
            $table->foreign('id_period')->references('id_period')->on('periods')->onDelete('cascade');

            // Mencegah triwulan yang sama dimasukkan dua kali pada risiko yang sama
            $table->unique(['id_monitoring', 'id_period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dep_monitoring_periods');
    }
};
