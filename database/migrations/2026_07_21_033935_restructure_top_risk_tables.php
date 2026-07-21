<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom baseline di tabel top_risiko
        Schema::table('top_risiko', function (Blueprint $table) {
            $table->integer('inherent')->after('is_aktif');
            $table->integer('target_tw1')->after('inherent');
            $table->integer('target_tw2')->after('target_tw1');
            $table->integer('target_tw3')->after('target_tw2');
            $table->integer('target_tw4')->after('target_tw3');
        });

        // 2. Hapus kolom target redundan di top_monitoring_bulanan (optional)
        Schema::table('top_monitoring_bulanan', function (Blueprint $table) {
            $table->dropColumn(['target_tw1', 'target_tw2', 'target_tw3', 'target_tw4']);
        });
    }

    public function down(): void
    {
        Schema::table('top_risiko', function (Blueprint $table) {
            $table->dropColumn(['inherent', 'target_tw1', 'target_tw2', 'target_tw3', 'target_tw4']);
        });

        Schema::table('top_monitoring_bulanan', function (Blueprint $table) {
            $table->integer('target_tw1')->nullable();
            $table->integer('target_tw2')->nullable();
            $table->integer('target_tw3')->nullable();
            $table->integer('target_tw4')->nullable();
        });
    }
};
