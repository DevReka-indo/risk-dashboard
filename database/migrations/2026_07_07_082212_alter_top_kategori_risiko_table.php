<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kategori_risiko', function (Blueprint $table) {
            // 1. Tambahkan kolom 'type' jika belum ada
            if (! Schema::hasColumn('kategori_risiko', 'type')) {
                $table->string('type', 50)->nullable()->after('nama_kategori');
            }
        });

        // 2. Cari nama index unique secara native lewat query MySQL
        $logicalDbName = DB::connection()->getDatabaseName();
        $indexStructure = DB::select("
            SELECT INDEX_NAME
            FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA = ?
            AND TABLE_NAME = 'kategori_risiko'
            AND COLUMN_NAME = 'nama_kategori'
        ", [$logicalDbName]);

        // 3. Jika index ditemukan, hapus index tersebut
        if (! empty($indexStructure)) {
            $indexName = $indexStructure[0]->INDEX_NAME;

            Schema::table('kategori_risiko', function (Blueprint $table) use ($indexName) {
                // Jika index-nya adalah PRIMARY, jangan dihapus. Hapus jika itu index unique biasa
                if ($indexName !== 'PRIMARY') {
                    $table->dropUnique($indexName);
                }
            });
        }

        // 4. Buat composite unique yang baru (nama_kategori + type)
        Schema::table('kategori_risiko', function (Blueprint $table) {
            $table->unique(['nama_kategori', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategori_risiko', function (Blueprint $table) {
            $table->dropUnique(['nama_kategori', 'type']);
            $table->unique('nama_kategori');
        });
    }
};
