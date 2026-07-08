<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RiskDashboardSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Tabel level_risiko (Sesuai Gambar 1)
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

        // Menggunakan updateOrInsert agar data tidak duplikat jika seeder dijalankan ulang
        foreach ($levels as $level) {
            DB::table('level_risiko')->updateOrInsert(['id_level' => $level['id_level']], $level);
        }

        // 2. Seed Tabel kategori_risiko (Sesuai Gambar 2)
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

        // 3. Dummy Seed untuk dep_monitoring & smap_monitoring (Opsional)
        // Pastikan id_unit = 1 sudah ada di tabel top_unit_kerja Anda sebelum menjalankan ini

        DB::table('dep_monitoring')->insert([
            'id_unit' => 1,
            'id_kategori' => 2, // Operasional
            'id_level' => 3,    // Moderate
            'risk_event_deta' => 'Kegagalan sistem server utama selama 2 jam.',
            'value' => 50000000,
            'inherent' => 4,
            'trend' => 'Stabil',
            'status' => true,
            'type' => 'Non-Proyek',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('smap_monitoring')->insert([
            'id_unit' => 1,
            'id_kategori' => 6, // Kepatuhan
            'id_level' => 1,    // Low
            'risk_event_deta' => 'Keterlambatan pelaporan dokumen triwulan.',
            'value' => 0,
            'inherent' => 2,
            'trend' => 'Turun',
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
