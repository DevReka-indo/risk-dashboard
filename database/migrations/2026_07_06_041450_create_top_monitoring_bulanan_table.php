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
        Schema::create('top_monitoring_bulanan', function (Blueprint $table): void {
            $table->id('id_monitoring');

            $table->foreignId('id_risiko')
                ->constrained('top_risiko', 'id_risiko')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('bulan');
            $table->unsignedSmallInteger('tahun');
            $table->integer('nilai');

            $table->foreignId('id_level')
                ->constrained('top_level_risiko', 'id_level')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Aktif');

            $table->unsignedInteger('progres_belum')->default(0);
            $table->unsignedInteger('progres_proses')->default(0);
            $table->unsignedInteger('progres_sudah')->default(0);

            $table->foreignId('id_aturan_efektivitas')
                ->nullable()
                ->constrained('top_aturan_efektivitas', 'id_aturan')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(
                ['id_risiko', 'bulan', 'tahun'],
                'top_monitoring_periode_unique'
            );

            $table->index(['bulan', 'tahun']);
            $table->index('id_level');
            $table->index('status');
            $table->index('id_aturan_efektivitas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('top_monitoring_bulanan');
    }
};
