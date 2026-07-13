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
        Schema::table('smap_monitoring_periods', function (Blueprint $table) {
            // Kita tambah dua kolom ini dengan sifat nullable() agar aman jika belum diisi
            $table->integer('inherent_target')->nullable()->after('inherent');
            $table->unsignedBigInteger('id_level_target')->nullable()->after('id_level');

            // Opsional: Jika tabel level_risiko menggunakan foreign key constraint
            // $table->foreign('id_level_target')->references('id_level')->on('level_risiko')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('smap_monitoring_periods', function (Blueprint $table) {
            // Untuk rollback jika suatu saat dibutuhkan
            $table->dropColumn(['inherent_target', 'id_level_target']);
        });
    }
};
