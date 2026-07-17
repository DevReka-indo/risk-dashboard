<?php

namespace App\Services;

use App\Models\LevelRisiko;
use App\Models\TopAturanEfektivitas;
use App\Models\TopMonitoringBulanan;
use App\Models\TopRisiko;
use Illuminate\Support\Facades\DB;

class TopRiskService
{
    //[cite: 1] Membungkus proses pembuatan ke dalam DB transaction.
    public function createTopRisk(array $data): TopRisiko
    {
        return DB::transaction(function () use ($data): TopRisiko {
            $topRisk = TopRisiko::query()->create([
                'nama_peristiwa_risiko' => $data['nama_peristiwa_risiko'],
                'id_kategori' => $data['id_kategori'],
                'tanggal_dibuat' => $data['tanggal_dibuat'],
                'is_aktif' => $data['is_aktif'] ?? true,
            ]);

            $topRisk->unitKerja()->sync($data['unit_kerja']);

            return $topRisk;
        });
    }

    //[cite: 1] Membungkus proses pembaruan ke dalam DB transaction.
    public function updateTopRisk(TopRisiko $topRisk, array $data): void
    {
        DB::transaction(function () use ($data, $topRisk): void {
            TopRisiko::query()
                ->where('id_risiko', $topRisk->id_risiko)
                ->update([
                    'nama_peristiwa_risiko' => $data['nama_peristiwa_risiko'],
                    'id_kategori' => $data['id_kategori'],
                    'tanggal_dibuat' => $data['tanggal_dibuat'],
                    'is_aktif' => $data['is_aktif'] ?? false,
                ]);

            $topRisk->unitKerja()->sync($data['unit_kerja']);
        });
    }

    public function storeMonitoring(TopRisiko $topRisk, array $data): void
    {
        //[cite: 1] Menyelesaikan pencarian ID level dan Aturan Efektivitas.
        $idLevel = $this->resolveLevelRisikoIdByNilai((int) $data['nilai']);
        $idAturanEfektivitas = $this->resolveAturanEfektivitasId(
            $topRisk->id_risiko, (int) $data['bulan'], (int) $data['tahun'], (int) $data['nilai'], $idLevel
        );

        TopMonitoringBulanan::query()->create([
            'id_risiko' => $topRisk->id_risiko,
            'bulan' => $data['bulan'],
            'tahun' => $data['tahun'],
            'nilai' => $data['nilai'],
            'id_level' => $idLevel,
            'status' => $data['status'],
            'progres_belum' => $data['progres_belum'] ?? 0,
            'progres_proses' => $data['progres_proses'] ?? 0,
            'progres_sudah' => $data['progres_sudah'] ?? 0,
            'id_aturan_efektivitas' => $idAturanEfektivitas,
            'catatan' => $data['catatan'] ?? null,
        ]);
    }

    public function updateMonitoring(TopMonitoringBulanan $monitoring, array $data): void
    {
        //[cite: 1] Pembaruan monitoring menggunakan logika perolehan ID level yang sama.
        $idLevel = $this->resolveLevelRisikoIdByNilai((int) $data['nilai']);
        $idAturanEfektivitas = $this->resolveAturanEfektivitasId(
            $monitoring->id_risiko, (int) $data['bulan'], (int) $data['tahun'], (int) $data['nilai'], $idLevel
        );

        TopMonitoringBulanan::query()
            ->where('id_monitoring', $monitoring->id_monitoring)
            ->update([
                'bulan' => $data['bulan'],
                'tahun' => $data['tahun'],
                'nilai' => $data['nilai'],
                'id_level' => $idLevel,
                'status' => $data['status'],
                'progres_belum' => $data['progres_belum'] ?? 0,
                'progres_proses' => $data['progres_proses'] ?? 0,
                'progres_sudah' => $data['progres_sudah'] ?? 0,
                'id_aturan_efektivitas' => $idAturanEfektivitas,
                'catatan' => $data['catatan'] ?? null,
            ]);
    }

    //[cite: 1] Logika penetapan range level.
    private function resolveLevelRisikoIdByNilai(int $nilai): int
    {
        $urutanLevel = match (true) {
            $nilai >= 20 && $nilai <= 25 => 5,
            $nilai >= 16 && $nilai <= 19 => 4,
            $nilai >= 11 && $nilai <= 15 => 3,
            $nilai >= 6 && $nilai <= 10 => 2,
            $nilai >= 1 && $nilai <= 5 => 1,
            default => throw new \InvalidArgumentException('Nilai risiko tidak valid.'),
        };

        return (int) LevelRisiko::query()->where('urutan', $urutanLevel)->valueOrFail('id_level');
    }

    //[cite: 1] Logika penarikan aturan efektivitas dari kueri periode bulan sebelumnya.
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

    //[cite: 1] Fungsi perbandingan nilai untuk resolusi efektivitas.
    private function compareValue(int $current, int $previous): string
    {
        if ($current < $previous) { return '<'; }
        if ($current > $previous) { return '>'; }
        return '=';
    }
}
