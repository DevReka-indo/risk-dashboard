<?php

namespace App\Services;

use App\Models\LevelRisiko;
use App\Models\TopAturanEfektivitas;
use App\Models\TopMonitoringBulanan;
use App\Models\TopRisiko;
use Illuminate\Support\Facades\DB;

class TopRiskService
{
    // Membungkus proses pembuatan data master risiko beserta baseline awal (inherent & target_tw1..4).
    public function createTopRisk(array $data): TopRisiko
    {
        return DB::transaction(function () use ($data): TopRisiko {
            $topRisk = TopRisiko::query()->create([
                'nama_peristiwa_risiko' => $data['nama_peristiwa_risiko'],
                'id_kategori'           => $data['id_kategori'],
                'tanggal_dibuat'        => $data['tanggal_dibuat'],
                'is_aktif'              => $data['is_aktif'] ?? true,
                'inherent'              => $data['inherent'],
                'target_tw1'            => $data['target_tw1'],
                'target_tw2'            => $data['target_tw2'],
                'target_tw3'            => $data['target_tw3'],
                'target_tw4'            => $data['target_tw4'],
            ]);

            $topRisk->unitKerja()->sync($data['unit_kerja']);

            return $topRisk;
        });
    }

    // Membungkus proses pembaruan ke dalam DB transaction.
    public function updateTopRisk(TopRisiko $topRisk, array $data): void
    {
        DB::transaction(function () use ($data, $topRisk): void {
            TopRisiko::query()
                ->where('id_risiko', $topRisk->id_risiko)
                ->update([
                    'nama_peristiwa_risiko' => $data['nama_peristiwa_risiko'],
                    'id_kategori'           => $data['id_kategori'],
                    'tanggal_dibuat'        => $data['tanggal_dibuat'],
                    'is_aktif'              => $data['is_aktif'] ?? false,
                    'inherent'              => $data['inherent'] ?? $topRisk->inherent,
                    'target_tw1'            => $data['target_tw1'] ?? $topRisk->target_tw1,
                    'target_tw2'            => $data['target_tw2'] ?? $topRisk->target_tw2,
                    'target_tw3'            => $data['target_tw3'] ?? $topRisk->target_tw3,
                    'target_tw4'            => $data['target_tw4'] ?? $topRisk->target_tw4,
                ]);

            $topRisk->unitKerja()->sync($data['unit_kerja']);
        });
    }

    public function storeMonitoring(TopRisiko $topRisk, array $data): void
    {
        // 1. Cari riwayat monitoring periode paling akhir sebelum periode yang sedang diinput
        $selectedPeriod = sprintf('%04d-%02d-01', (int) $data['tahun'], (int) $data['bulan']);

        $monitoringSebelumnya = TopMonitoringBulanan::query()
            ->where('id_risiko', $topRisk->id_risiko)
            ->whereRaw("STR_TO_DATE(CONCAT(tahun, '-', LPAD(bulan, 2, '0'), '-01'), '%Y-%m-%d') < ?", [$selectedPeriod])
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->first();

        // 2. Logika Inherent Dinamis Cascading:
        // - Jika ada monitoring sebelumnya & nilainya > 0 -> Inherent = Nilai realisasi bulan sebelumnya.
        // - Jika input pertama kali -> Inherent = Baseline awal dari Master TopRisiko.
        if ($monitoringSebelumnya && (int) $monitoringSebelumnya->nilai > 0) {
            $inherent = (int) $monitoringSebelumnya->nilai;
        } else {
            $inherent = (int) ($topRisk->inherent ?? $data['inherent'] ?? 0);
        }

        // 3. Hitung level dan aturan efektivitas
        $idLevel = $this->resolveLevelRisikoIdByNilai((int) $data['nilai']);
        $idAturanEfektivitas = $this->resolveAturanEfektivitasId(
            $topRisk->id_risiko, (int) $data['bulan'], (int) $data['tahun'], (int) $data['nilai'], $idLevel
        );

        // 4. Simpan record monitoring bulanan
        TopMonitoringBulanan::query()->create([
            'id_risiko'             => $topRisk->id_risiko,
            'bulan'                 => $data['bulan'],
            'tahun'                 => $data['tahun'],
            'nilai'                 => $data['nilai'],
            'id_level'              => $idLevel,
            'status'                => $data['status'],
            'inherent'              => $inherent, // Inherent dinamis tersimpan di sini
            'progres_belum'         => $data['progres_belum'] ?? 0,
            'progres_proses'        => $data['progres_proses'] ?? 0,
            'progres_sudah'         => $data['progres_sudah'] ?? 0,
            'id_aturan_efektivitas' => $idAturanEfektivitas,
            'catatan'               => $data['catatan'] ?? null,
        ]);
    }

    public function updateMonitoring(TopMonitoringBulanan $monitoring, array $data): void
    {
        // Pembaruan monitoring menggunakan logika perolehan ID level yang sama.
        $idLevel = $this->resolveLevelRisikoIdByNilai((int) $data['nilai']);
        $idAturanEfektivitas = $this->resolveAturanEfektivitasId(
            $monitoring->id_risiko, (int) $data['bulan'], (int) $data['tahun'], (int) $data['nilai'], $idLevel
        );

        TopMonitoringBulanan::query()
            ->where('id_monitoring', $monitoring->id_monitoring)
            ->update([
                'bulan'                 => $data['bulan'],
                'tahun'                 => $data['tahun'],
                'nilai'                 => $data['nilai'],
                'id_level'              => $idLevel,
                'status'                => $data['status'],
                'inherent'              => $data['inherent'] ?? $monitoring->inherent,
                'progres_belum'         => $data['progres_belum'] ?? 0,
                'progres_proses'        => $data['progres_proses'] ?? 0,
                'progres_sudah'         => $data['progres_sudah'] ?? 0,
                'id_aturan_efektivitas' => $idAturanEfektivitas,
                'catatan'               => $data['catatan'] ?? null,
            ]);
    }

    // Logika penetapan range level.
    public function resolveLevelRisikoIdByNilai(int $nilai): int
    {
        $urutanLevel = match (true) {
            $nilai >= 20 && $nilai <= 25 => 5,
            $nilai >= 16 && $nilai <= 19 => 4,
            $nilai >= 12 && $nilai <= 15 => 3,
            $nilai >= 6  && $nilai <= 11 => 2, 
            $nilai >= 1  && $nilai <= 5  => 1,
            default => throw new \InvalidArgumentException('Nilai risiko tidak valid.'),
        };

        return (int) LevelRisiko::query()->where('urutan', $urutanLevel)->valueOrFail('id_level');
    }

    // Logika penarikan aturan efektivitas dari kueri periode bulan sebelumnya.
    private function resolveAturanEfektivitasId(int $idRisiko, int $bulan, int $tahun, int $nilaiBulanIni, int $idLevelBulanIni): ?int
    {
        $periodeSekarang = sprintf('%04d-%02d-01', $tahun, $bulan);

        $monitoringSebelumnya = TopMonitoringBulanan::query()
            ->with('level')
            ->where('id_risiko', $idRisiko)
            ->whereRaw("STR_TO_DATE(CONCAT(tahun, '-', LPAD(bulan, 2, '0'), '-01'), '%Y-%m-%d') < ?", [$periodeSekarang])
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->first();

        if ($monitoringSebelumnya === null || $monitoringSebelumnya->level === null) {
            return null;
        }

        $levelBulanIni = LevelRisiko::query()->where('id_level', $idLevelBulanIni)->firstOrFail();

        $kondisiNilai = $this->compareValue($nilaiBulanIni, (int) $monitoringSebelumnya->nilai);
        $kondisiLevel = $this->compareValue((int) $levelBulanIni->urutan, (int) $monitoringSebelumnya->level->urutan);

        $jenisAturan = match (true) {
            (int) $levelBulanIni->urutan <= 2 && $kondisiNilai === '=' && $kondisiLevel === '=' => 'level_rendah',
            (int) $levelBulanIni->urutan <= 2 && $kondisiNilai === '<' && in_array($kondisiLevel, ['<', '='], true) => 'level_rendah',
            default => 'umum',
        };

        return TopAturanEfektivitas::query()
            ->where('kondisi_nilai', $kondisiNilai)
            ->where('kondisi_level', $kondisiLevel)
            ->where('jenis_aturan', $jenisAturan)
            ->value('id_aturan');
    }

    // Fungsi perbandingan nilai untuk resolusi efektivitas.
    private function compareValue(int $current, int $previous): string
    {
        if ($current < $previous) { return '<'; }
        if ($current > $previous) { return '>'; }
        return '=';
    }
}
