<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Exception;

class DepartmentRiskService
{
    /**
     * Menyimpan atau mengupdate data periode/triwulan monitoring risiko.
     *
     * @param int $id_monitoring
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function updateOrCreatePeriod($id_monitoring, array $data)
    {
        // Gunakan Transaction agar jika terjadi error, data tidak tersimpan setengah-setengah
        DB::beginTransaction();

        try {
            // 1. Tentukan Kuartal dan Tahun saat ini dari inputan form
            $currentQuarter = $data['quarter'];
            $currentYear = $data['year'];

            // 2. Tentukan Kuartal dan Tahun sebelumnya (Mundur 1 Step)
            $prevQuarter = match($currentQuarter) {
                'TW4' => 'TW3',
                'TW3' => 'TW2',
                'TW2' => 'TW1',
                'TW1' => 'TW4',
                default => null
            };

            // Jika kuartal saat ini adalah TW1, maka tahun sebelumnya dikurangi 1
            $prevYear = ($currentQuarter === 'TW1') ? $currentYear - 1 : $currentYear;

            // 3. Cari histori triwulan sebelumnya di database
            $prevPeriod = DB::table('dep_monitoring_periods')
                ->where('id_monitoring', $id_monitoring)
                ->where('quarter', $prevQuarter)
                ->where('year', $prevYear)
                ->first();

            // 4. Tentukan Nilai Inheren secara sistem
            if ($prevPeriod) {
                // Jika ada histori sebelumnya, nilai inherent saat ini = nilai (value) triwulan lalu
                $inherentValue = $prevPeriod->value;
            } else {
                // Jika tidak ada histori (baru pertama kali diinput), ambil dari master risk
                $masterRisk = DB::table('dep_monitoring')
                    ->where('id_monitoring', $id_monitoring)
                    ->first();

                $inherentValue = $masterRisk ? $masterRisk->inherent : null;
            }

            // 5. Simpan (Insert) atau Perbarui (Update) data ke tabel pivot/periode
            DB::table('dep_monitoring_periods')->updateOrInsert(
                [
                    // Kondisi pencarian (jika data dengan id, quarter, dan year ini ada, maka diupdate)
                    'id_monitoring' => $id_monitoring,
                    'quarter'       => $currentQuarter,
                    'year'          => $currentYear,
                ],
                [
                    // Data yang diperbarui atau ditambahkan
                    'inherent'        => $inherentValue, // <-- Nilai hasil logika otomatis
                    'value'           => $data['value'],
                    'progres_belum'   => $data['progres_belum'] ?? 0,
                    'progres_proses'  => $data['progres_proses'] ?? 0,
                    'progres_sudah'   => $data['progres_sudah'] ?? 0,
                    'level'           => $data['calculated_level'] ?? null,
                    'trend'           => $data['calculated_trend'] ?? null,
                    'target_value'    => $data['target_value'] ?? null,
                    'target_id_level' => $data['target_id_level'] ?? null,
                    'updated_at'      => now(),
                ]
            );

            // 6. Update status monitoring di tabel master (dep_monitoring)
            if (isset($data['status_monitoring'])) {
                DB::table('dep_monitoring')
                    ->where('id_monitoring', $id_monitoring)
                    ->update([
                        'status'     => $data['status_monitoring'],
                        'updated_at' => now()
                    ]);
            }

            // Commit perubahan jika semua lancar
            DB::commit();
            return true;

        } catch (Exception $e) {
            // Batalkan semua perubahan database jika terjadi error
            DB::rollBack();
            throw $e;
        }
    }
}
