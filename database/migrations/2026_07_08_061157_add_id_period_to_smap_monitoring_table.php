<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('smap_monitoring', function (Blueprint $table) {
            // Wajib ->nullable() agar data SMAP yang sudah ada tidak error saat dijalankan
            $table->foreignId('id_period')
                  ->nullable()
                  ->after('status') // Meletakkan kolom setelah 'status'
                  ->constrained('periods', 'id_period')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('smap_monitoring', function (Blueprint $table) {
            $table->dropForeign(['id_period']);
            $table->dropColumn('id_period');
        });
    }
};
