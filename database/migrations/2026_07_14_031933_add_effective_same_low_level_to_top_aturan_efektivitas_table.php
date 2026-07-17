<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('top_aturan_efektivitas')->updateOrInsert(
            [
                'kondisi_nilai' => '<',
                'kondisi_level' => '=',
                'jenis_aturan' => 'level_rendah',
            ],
            [
                'hasil' => 'Effective',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('top_aturan_efektivitas')
            ->where('kondisi_nilai', '<')
            ->where('kondisi_level', '=')
            ->where('jenis_aturan', 'level_rendah')
            ->where('hasil', 'Effective')
            ->delete();
    }
};