<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mengubah nama tabel lama ke nama tabel baru
        Schema::rename('top_kategori_risiko', 'kategori_risiko');
        Schema::rename('top_level_risiko', 'level_risiko');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan nama tabel jika di-rollback
        Schema::rename('kategori_risiko', 'top_kategori_risiko');
        Schema::rename('level_risiko', 'top_level_risiko');
    }
};
