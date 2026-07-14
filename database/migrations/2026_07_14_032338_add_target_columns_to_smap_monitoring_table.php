<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('smap_monitoring', function (Blueprint $table) {
            // Menambahkan inherent_target persis setelah kolom inherent yang sudah ada
            if (!Schema::hasColumn('smap_monitoring', 'inherent_target')) {
                $table->integer('inherent_target')->default(0)->after('inherent');
            }

            // Menambahkan id_level_target setelah kolom id_level yang sudah ada
            if (!Schema::hasColumn('smap_monitoring', 'id_level_target')) {
                $table->unsignedBigInteger('id_level_target')->nullable()->after('id_level');

                // Opsional: Tambahkan foreign key constraint jika id_level_target merujuk ke tabel level_risiko
                // $table->foreign('id_level_target')->references('id_level')->on('level_risiko')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('smap_monitoring', function (Blueprint $table) {
            // Rollback drop foreign key jika kamu mengaktifkannya di atas
            // $table->dropForeign(['id_level_target']);

            $table->dropColumn(['inherent_target', 'id_level_target']);
        });
    }
};
