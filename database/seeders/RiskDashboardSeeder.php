<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RiskDashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Tabel Master: top_unit_kerja (Hanya mengambil/mengisi nama_unit)
        $units = [
            ['nama_unit' => 'Departemen SDM'],
            ['nama_unit' => 'Departemen Hukum'],
            ['nama_unit' => 'Departemen Operasional'],
            ['nama_unit' => 'Departemen Keuangan'],
            ['nama_unit' => 'Departemen IT'],
        ];
        DB::table('top_unit_kerja')->insert($units);

        // 2. Seed Tabel Master: vds_level
        // Memasukkan gabungan opsi kapital (untuk DPT) dan huruf kecil (untuk SMAP) jika dibutuhkan,
        // namun disesuaikan dengan isi blueprint enum yang kamu buat.
        $levels = [
            ['level_name' => 'Low'],
            ['level_name' => 'Low to Moderate'],
            ['level_name' => 'Moderate'],
            ['level_name' => 'Moderate to High'],
            ['level_name' => 'High'],
        ];
        DB::table('vds_level')->insert($levels);

        // 3. Seed Tabel Master: vds_categorie
        $categories = [
            ['category_name' => 'Operasional'],
            ['category_name' => 'Strategis'],
            ['category_name' => 'Finansial'],
            ['category_name' => 'Kepatuhan (Compliance)'],
        ];
        DB::table('vds_categorie')->insert($categories);

        // 4. Seed Tabel Utama: vdpt_monitoring (Contoh Data DPT)
        DB::table('vdpt_monitoring')->insert([
            [
                'id_unit' => 1, // Mengacu ke Departemen SDM
                'id_category' => 1, // Mengacu ke Operasional
                'id_level' => 3, // Mengacu ke Moderate
                'risk_event_deta' => 'Keterlambatan pemenuhan kuota rekrutmen karyawan inti.',
                'value' => 12,
                'inherent' => 15,
                'trend' => 'Naik',
                'status' => true,
                'type' => 'Non-Proyek',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_unit' => 5, // Mengacu ke Departemen IT
                'id_category' => 2, // Mengacu ke Strategis
                'id_level' => 5, // Mengacu ke High
                'risk_event_deta' => 'Kegagalan migrasi server core dashboard ke infrastruktur baru.',
                'value' => 20,
                'inherent' => 25,
                'trend' => 'Stabil',
                'status' => true,
                'type' => 'Proyek',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 5. Seed Tabel Utama: vsmap_monitoring (Contoh Data SMAP)
        // Catatan: Karena enum 'trend' dan 'status' di tabel SMAP kamu menggunakan huruf kecil,
        // datanya disesuaikan menjadi 'turun'/'stagnan' dan 'aktif'/'nonaktif'.
        DB::table('vsmap_monitoring')->insert([
            [
                'id_unit' => 2, // Mengacu ke Departemen Hukum
                'id_category' => 4, // Mengacu ke Kepatuhan
                'id_level' => 2, // Mengacu ke Low to Moderate
                'risk_event' => 'Keterlambatan pembaruan sertifikasi ISO anti-penyuapan.',
                'value' => 6,
                'inherent' => 10,
                'trend' => 'turun',
                'status' => 'aktif',
                'created_at' => now(),
            ],
            [
                'id_unit' => 3, // Mengacu ke Departemen Operasional
                'id_category' => 1, // Mengacu ke Operasional
                'id_level' => 4, // Mengacu ke Moderate to High
                'risk_event' => 'Adanya temuan konflik kepentingan pada proses pengadaan vendor.',
                'value' => 16,
                'inherent' => 16,
                'trend' => 'stagnan',
                'status' => 'aktif',
                'created_at' => now(),
            ],
        ]);
    }
}
