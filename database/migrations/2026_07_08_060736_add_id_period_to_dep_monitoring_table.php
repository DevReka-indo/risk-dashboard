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
            // Menambahkan foreign key id_period setelah kolom type
            $table->unsignedBigInteger('id_period')->nullable()->after('type');

            // Setup relasi ke tabel periods
            $table->foreign('id_period')->references('id_period')->on('periods')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('dep_monitoring', function (Blueprint $table) {
            $table->dropForeign(['id_period']);
            $table->dropColumn('id_period');
        });
    }
};
