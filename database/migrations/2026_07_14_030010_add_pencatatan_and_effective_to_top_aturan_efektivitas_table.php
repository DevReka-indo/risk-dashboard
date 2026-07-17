<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('top_aturan_efektivitas', function (Blueprint $table): void {
            $table
                ->string('jenis_aturan', 30)
                ->default('umum')
                ->after('kondisi_level');
        });

        Schema::table('top_aturan_efektivitas', function (Blueprint $table): void {
            $table->dropUnique('top_aturan_efektivitas_unique');

            $table->unique(
                ['kondisi_nilai', 'kondisi_level', 'jenis_aturan'],
                'top_aturan_efektivitas_unique'
            );
        });

        DB::table('top_aturan_efektivitas')->insert([
            [
                'kondisi_nilai' => '=',
                'kondisi_level' => '=',
                'jenis_aturan' => 'level_rendah',
                'hasil' => 'Pencatatan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kondisi_nilai' => '<',
                'kondisi_level' => '<',
                'jenis_aturan' => 'level_rendah',
                'hasil' => 'Effective',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('top_aturan_efektivitas')
            ->where('jenis_aturan', 'level_rendah')
            ->whereIn('hasil', ['Pencatatan', 'Effective'])
            ->delete();

        Schema::table('top_aturan_efektivitas', function (Blueprint $table): void {
            $table->dropUnique('top_aturan_efektivitas_unique');

            $table->unique(
                ['kondisi_nilai', 'kondisi_level'],
                'top_aturan_efektivitas_unique'
            );

            $table->dropColumn('jenis_aturan');
        });
    }
};