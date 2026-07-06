<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TopRiskInitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $now = now();

            /*
            |--------------------------------------------------------------------------
            | Master Level Risiko
            |--------------------------------------------------------------------------
            */
            DB::table('top_level_risiko')->upsert([
                [
                    'id_level' => 1,
                    'nama_level' => 'Low',
                    'urutan' => 1,
                    'kode_warna' => '#00B050',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id_level' => 2,
                    'nama_level' => 'Low to Moderate',
                    'urutan' => 2,
                    'kode_warna' => '#92D050',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id_level' => 3,
                    'nama_level' => 'Moderate',
                    'urutan' => 3,
                    'kode_warna' => '#FFC000',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id_level' => 4,
                    'nama_level' => 'Moderate to High',
                    'urutan' => 4,
                    'kode_warna' => '#ED7D31',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id_level' => 5,
                    'nama_level' => 'High',
                    'urutan' => 5,
                    'kode_warna' => '#FF0000',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ], ['id_level'], ['nama_level', 'urutan', 'kode_warna', 'updated_at']);

            /*
            |--------------------------------------------------------------------------
            | Master Aturan Efektivitas
            |--------------------------------------------------------------------------
            */
            DB::table('top_aturan_efektivitas')->upsert([
                [
                    'kondisi_nilai' => '<',
                    'kondisi_level' => '<',
                    'hasil' => 'Mostly Effective',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'kondisi_nilai' => '<',
                    'kondisi_level' => '=',
                    'hasil' => 'Partially Effective',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'kondisi_nilai' => '<',
                    'kondisi_level' => '>',
                    'hasil' => 'Partially Effective',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'kondisi_nilai' => '=',
                    'kondisi_level' => '<',
                    'hasil' => 'Mostly Effective',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'kondisi_nilai' => '=',
                    'kondisi_level' => '=',
                    'hasil' => 'In-Effective',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'kondisi_nilai' => '=',
                    'kondisi_level' => '>',
                    'hasil' => 'In-Effective',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'kondisi_nilai' => '>',
                    'kondisi_level' => '<',
                    'hasil' => 'Partially Effective',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'kondisi_nilai' => '>',
                    'kondisi_level' => '=',
                    'hasil' => 'In-Effective',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'kondisi_nilai' => '>',
                    'kondisi_level' => '>',
                    'hasil' => 'In-Effective',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ], ['kondisi_nilai', 'kondisi_level'], ['hasil', 'updated_at']);

            /*
            |--------------------------------------------------------------------------
            | Master Kategori Risiko
            |--------------------------------------------------------------------------
            */
            $kategoriNames = [
                'Strategis',
                'Operasional',
                'Keuangan',
                'Investasi',
            ];

            foreach ($kategoriNames as $kategoriName) {
                DB::table('top_kategori_risiko')->updateOrInsert(
                    ['nama_kategori' => $kategoriName],
                    [
                        'keterangan' => null,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Master Unit Kerja
            |--------------------------------------------------------------------------
            */
            $unitNames = [
                'Pemasaran',
                'Teknologi',
                'Logistik',
                'Operasi',
                'QCAS',
                'Keu & Akun',
                'Tim Investasi',
            ];

            foreach ($unitNames as $unitName) {
                DB::table('top_unit_kerja')->updateOrInsert(
                    ['nama_unit' => $unitName],
                    [
                        'keterangan' => null,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );
            }

            $kategori = DB::table('top_kategori_risiko')
                ->pluck('id_kategori', 'nama_kategori');

            $unit = DB::table('top_unit_kerja')
                ->pluck('id_unit', 'nama_unit');

            $level = DB::table('top_level_risiko')
                ->pluck('id_level', 'nama_level');

            $aturan = DB::table('top_aturan_efektivitas')
                ->get()
                ->keyBy(fn ($item): string => $item->kondisi_nilai.'|'.$item->kondisi_level);

            /*
            |--------------------------------------------------------------------------
            | Data Risiko
            |--------------------------------------------------------------------------
            */
            $risks = [
                [
                    'nama_peristiwa_risiko' => 'Kegagalan perolehan proyek INKA Grup',
                    'kategori' => 'Strategis',
                    'units' => ['Pemasaran'],
                    'monitoring' => [
                        [
                            'bulan' => 3,
                            'tahun' => 2026,
                            'nilai' => 19,
                            'level' => 'Moderate to High',
                            'status' => 'Aktif',
                            'progres_belum' => 5,
                            'progres_proses' => 7,
                            'progres_sudah' => 16,
                            'aturan_key' => null,
                        ],
                        [
                            'bulan' => 4,
                            'tahun' => 2026,
                            'nilai' => 14,
                            'level' => 'Moderate',
                            'status' => 'Aktif',
                            'progres_belum' => 0,
                            'progres_proses' => 0,
                            'progres_sudah' => 0,
                            'aturan_key' => '<|<',
                        ],
                    ],
                ],
                [
                    'nama_peristiwa_risiko' => 'Rilis dokumen pendukung pelaksanaan produksi tidak sesuai jadwal',
                    'kategori' => 'Operasional',
                    'units' => ['Teknologi'],
                    'monitoring' => [
                        [
                            'bulan' => 3,
                            'tahun' => 2026,
                            'nilai' => 22,
                            'level' => 'High',
                            'status' => 'Aktif',
                            'progres_belum' => 3,
                            'progres_proses' => 3,
                            'progres_sudah' => 11,
                            'aturan_key' => null,
                        ],
                        [
                            'bulan' => 4,
                            'tahun' => 2026,
                            'nilai' => 12,
                            'level' => 'Moderate',
                            'status' => 'Aktif',
                            'progres_belum' => 0,
                            'progres_proses' => 0,
                            'progres_sudah' => 0,
                            'aturan_key' => '<|<',
                        ],
                    ],
                ],
                [
                    'nama_peristiwa_risiko' => 'Keterlambatan kedatangan material',
                    'kategori' => 'Operasional',
                    'units' => ['Logistik'],
                    'monitoring' => [
                        [
                            'bulan' => 3,
                            'tahun' => 2026,
                            'nilai' => 25,
                            'level' => 'High',
                            'status' => 'Aktif',
                            'progres_belum' => 7,
                            'progres_proses' => 6,
                            'progres_sudah' => 16,
                            'aturan_key' => null,
                        ],
                        [
                            'bulan' => 4,
                            'tahun' => 2026,
                            'nilai' => 25,
                            'level' => 'High',
                            'status' => 'Aktif',
                            'progres_belum' => 0,
                            'progres_proses' => 0,
                            'progres_sudah' => 0,
                            'aturan_key' => '=|=',
                        ],
                    ],
                ],
                [
                    'nama_peristiwa_risiko' => 'Keterlambatan penerimaan pembayaran dari pelanggan',
                    'kategori' => 'Keuangan',
                    'units' => ['Operasi'],
                    'monitoring' => [
                        [
                            'bulan' => 3,
                            'tahun' => 2026,
                            'nilai' => 17,
                            'level' => 'Moderate to High',
                            'status' => 'Aktif',
                            'progres_belum' => 0,
                            'progres_proses' => 0,
                            'progres_sudah' => 3,
                            'aturan_key' => null,
                        ],
                        [
                            'bulan' => 4,
                            'tahun' => 2026,
                            'nilai' => 22,
                            'level' => 'High',
                            'status' => 'Aktif',
                            'progres_belum' => 0,
                            'progres_proses' => 0,
                            'progres_sudah' => 0,
                            'aturan_key' => '>|>',
                        ],
                    ],
                ],
                [
                    'nama_peristiwa_risiko' => 'Pembangunan workshop Fabrikasi Sukosari tidak sesuai jadwal',
                    'kategori' => 'Investasi',
                    'units' => ['QCAS', 'Keu & Akun', 'Tim Investasi'],
                    'monitoring' => [
                        [
                            'bulan' => 3,
                            'tahun' => 2026,
                            'nilai' => 18,
                            'level' => 'Moderate to High',
                            'status' => 'Aktif',
                            'progres_belum' => 0,
                            'progres_proses' => 5,
                            'progres_sudah' => 2,
                            'aturan_key' => null,
                        ],
                        [
                            'bulan' => 4,
                            'tahun' => 2026,
                            'nilai' => 22,
                            'level' => 'High',
                            'status' => 'Aktif',
                            'progres_belum' => 0,
                            'progres_proses' => 0,
                            'progres_sudah' => 0,
                            'aturan_key' => '>|>',
                        ],
                    ],
                ],
            ];

            foreach ($risks as $risk) {
                DB::table('top_risiko')->updateOrInsert(
                    ['nama_peristiwa_risiko' => $risk['nama_peristiwa_risiko']],
                    [
                        'id_kategori' => $kategori[$risk['kategori']],
                        'tanggal_dibuat' => '2026-03-01',
                        'is_aktif' => true,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );

                $idRisiko = DB::table('top_risiko')
                    ->where('nama_peristiwa_risiko', $risk['nama_peristiwa_risiko'])
                    ->value('id_risiko');

                foreach ($risk['units'] as $unitName) {
                    DB::table('top_risiko_unit_kerja')->updateOrInsert(
                        [
                            'id_risiko' => $idRisiko,
                            'id_unit' => $unit[$unitName],
                        ],
                        [
                            'updated_at' => $now,
                            'created_at' => $now,
                        ]
                    );
                }

                foreach ($risk['monitoring'] as $monitoring) {
                    DB::table('top_monitoring_bulanan')->updateOrInsert(
                        [
                            'id_risiko' => $idRisiko,
                            'bulan' => $monitoring['bulan'],
                            'tahun' => $monitoring['tahun'],
                        ],
                        [
                            'nilai' => $monitoring['nilai'],
                            'id_level' => $level[$monitoring['level']],
                            'status' => $monitoring['status'],
                            'progres_belum' => $monitoring['progres_belum'],
                            'progres_proses' => $monitoring['progres_proses'],
                            'progres_sudah' => $monitoring['progres_sudah'],
                            'id_aturan_efektivitas' => $monitoring['aturan_key'] !== null
                                ? $aturan[$monitoring['aturan_key']]->id_aturan
                                : null,
                            'catatan' => null,
                            'updated_at' => $now,
                            'created_at' => $now,
                        ]
                    );
                }
            }
        });
    }
}
