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
        Schema::table('dep_monitoring', function (Blueprint $table) {
            // Menghapus kolom penanganan
            $table->dropColumn('penanganan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dep_monitoring', function (Blueprint $table) {
            // Mengembalikan kolom jika dilakukan rollback (menyesuaikan tipe ENUM bawaan di database)
            $table->enum('penanganan', ['Belum', 'Proses', 'Selesai'])->default('Belum');
        });
    }
};
