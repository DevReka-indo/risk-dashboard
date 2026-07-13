<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('smap_monitoring_periods', function (Blueprint $table) {
            // Menambahkan kolom enum dengan default 'belum'
            $table->enum('status_penanganan', ['belum', 'proses', 'selesai'])->default('belum')->after('trend');
        });
    }

    public function down(): void
    {
        Schema::table('smap_monitoring_periods', function (Blueprint $table) {
            $table->dropColumn('status_penanganan');
        });
    }
};
