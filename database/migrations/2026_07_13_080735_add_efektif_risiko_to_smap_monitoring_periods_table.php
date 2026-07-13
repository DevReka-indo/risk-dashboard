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
            // Menambahkan kolom efektif_risiko bertipe ENUM dengan 6 nilai kondisi sesuai rumus Excel
            $table->enum('efektif_risiko', [
                'Pencatatan',
                'Effective',
                'Mostly Effective',
                'Partially Effective',
                'In-Effective',
                'Unmeasurable'
            ])->nullable()->after('trend'); // Diletakkan setelah kolom 'trend'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('smap_monitoring_periods', function (Blueprint $table) {
            // Menghapus kolom jika migration di-rollback
            $table->dropColumn('efektif_risiko');
        });
    }
};
