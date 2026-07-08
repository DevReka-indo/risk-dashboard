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
        Schema::table('top_kategori_risiko', function (Blueprint $table) {
            // Menambahkan kolom type. Anda bisa menyesuaikan tipe datanya (string/integer/enum)
            // Di sini saya pakai string, dan diletakkan setelah kolom 'nama_kategori'
            $table->string('type', 50)->nullable()->after('nama_kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('top_kategori_risiko', function (Blueprint $table) {
            // Drop kolom type jika migrasi ini di-rollback
            $table->dropColumn('type');
        });
    }
};
