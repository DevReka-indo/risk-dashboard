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
        Schema::table('dep_monitoring_periods', function (Blueprint $table) {
            // Menambahkan 3 kolom baru
            $table->integer('progres_belum')->default(0);
            $table->integer('progres_proses')->default(0);
            $table->integer('progres_sudah')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dep_monitoring_periods', function (Blueprint $table) {
            // Menghapus kembali kolom jika dilakukan rollback
            $table->dropColumn(['progres_belum', 'progres_proses', 'progres_sudah']);
        });
    }
};
