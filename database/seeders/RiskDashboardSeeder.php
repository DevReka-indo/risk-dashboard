<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RiskDashboardSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // TAMBAHAN: Seed Tabel periods (Master Periode)
        // ==========================================
        $periods = [
            [
                'id_period' => 1,
                'period_name' => 'TW1 2026',
                'year' => 2026,
                'quarter' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_period' => 2,
                'period_name' => 'TW2 2026',
                'year' => 2026,
                'quarter' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_period' => 3,
                'period_name' => 'TW3 2026',
                'year' => 2026,
                'quarter' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_period' => 4,
                'period_name' => 'TW4 2026',
                'year' => 2026,
                'quarter' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($periods as $period) {
            DB::table('periods')->updateOrInsert(['id_period' => $period['id_period']], $period);
        }

        // 1. Seed Tabel level_risiko
        $levels = [
            [
                'id_level' => 1,
                'nama_level' => 'Low',
                'urutan' => 1,
                'kode_warna' => '#00B050',
                'created_at' => '2026-07-06 04:23:49',
                'updated_at' => '2026-07-06 04:23:49',
            ],
            [
                'id_level' => 2,
                'nama_level' => 'Low to Moderate',
                'urutan' => 2,
                'kode_warna' => '#92D050',
                'created_at' => '2026-07-06 04:23:49',
                'updated_at' => '2026-07-06 04:23:49',
            ],
            [
                'id_level' => 3,
                'nama_level' => 'Moderate',
                'urutan' => 3,
                'kode_warna' => '#FFC000',
                'created_at' => '2026-07-06 04:23:49',
                'updated_at' => '2026-07-06 04:23:49',
            ],
            [
                'id_level' => 4,
                'nama_level' => 'Moderate to High',
                'urutan' => 4,
                'kode_warna' => '#ED7D31',
                'created_at' => '2026-07-06 04:23:49',
                'updated_at' => '2026-07-06 04:23:49',
            ],
            [
                'id_level' => 5,
                'nama_level' => 'High',
                'urutan' => 5,
                'kode_warna' => '#FF0000',
                'created_at' => '2026-07-06 04:23:49',
                'updated_at' => '2026-07-06 04:23:49',
            ],
        ];

        foreach ($levels as $level) {
            DB::table('level_risiko')->updateOrInsert(['id_level' => $level['id_level']], $level);
        }

        // 2. Seed Tabel kategori_risiko
        $kategori = [
            [
                'id_kategori' => 1,
                'nama_kategori' => 'Strategis',
                'type' => 'departement',
                'keterangan' => null,
                'created_at' => '2026-07-06 04:23:49',
                'updated_at' => '2026-07-06 04:23:49',
            ],
            [
                'id_kategori' => 2,
                'nama_kategori' => 'Operasional',
                'type' => 'departement',
                'keterangan' => null,
                'created_at' => '2026-07-06 04:23:49',
                'updated_at' => '2026-07-06 04:23:49',
            ],
            [
                'id_kategori' => 3,
                'nama_kategori' => 'Keuangan',
                'type' => 'departement',
                'keterangan' => null,
                'created_at' => '2026-07-06 04:23:49',
                'updated_at' => '2026-07-06 04:23:49',
            ],
            [
                'id_kategori' => 4,
                'nama_kategori' => 'Investasi',
                'type' => 'departement',
                'keterangan' => null,
                'created_at' => '2026-07-06 04:23:49',
                'updated_at' => '2026-07-06 04:23:49',
            ],
            [
                'id_kategori' => 5,
                'nama_kategori' => 'Finansial',
                'type' => 'departement',
                'keterangan' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id_kategori' => 6,
                'nama_kategori' => 'Hukum',
                'type' => 'departement',
                'keterangan' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id_kategori' => 7,
                'nama_kategori' => 'Hukum',
                'type' => 'smap',
                'keterangan' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
        ];

        foreach ($kategori as $kat) {
            DB::table('kategori_risiko')->updateOrInsert(['id_kategori' => $kat['id_kategori']], $kat);
        }

        // 3. Dummy Seed untuk dep_monitoring & smap_monitoring
        // 💡 REVISI: Mengikuti aturan baru, saat awal ditambahkan, periode diset null (kosong)
        DB::table('dep_monitoring')->insert([
            'id_unit' => 1,
            'id_kategori' => 2, // Operasional
            'id_level' => 3,    // Moderate
            'risk_event_deta' => 'Kegagalan sistem server utama selama 2 jam.',
            'value' => 12,
            'inherent' => 4,
            'trend' => 'Stabil',
            'status' => true,
            'type' => 'Non-Proyek',
            'id_period' => null, // 💡 Periode dikosongkan terlebih dahulu sesuai revisi Anda
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('smap_monitoring')->insert([
            'id_unit' => 1,
            'id_kategori' => 6, // Hukum/Kepatuhan
            'id_level' => 1,    // Low
            'risk_event_deta' => 'Keterlambatan pelaporan dokumen triwulan.',
            'value' => 0,
            'inherent' => 2,
            'trend' => 'Turun',
            'status' => true,
            'id_period' => null, // 💡 Periode dikosongkan terlebih dahulu sesuai revisi Anda
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
