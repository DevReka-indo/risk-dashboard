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
        Schema::table('top_monitoring_bulanan', function (Blueprint $table) {
            // Menambahkan kolom inherent dan 4 target triwulan
            $table->integer('inherent')->nullable()->after('id_risiko');
            $table->integer('target_tw1')->nullable()->after('inherent');
            $table->integer('target_tw2')->nullable()->after('target_tw1');
            $table->integer('target_tw3')->nullable()->after('target_tw2');
            $table->integer('target_tw4')->nullable()->after('target_tw3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('top_monitoring_bulanan', function (Blueprint $table) {
            // Menghapus kolom jika migration di-rollback
            $table->dropColumn([
                'inherent',
                'target_tw1',
                'target_tw2',
                'target_tw3',
                'target_tw4',
            ]);
        });
    }
};
