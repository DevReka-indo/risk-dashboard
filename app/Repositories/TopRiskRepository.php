<?php

namespace App\Repositories;

use App\Models\KategoriRisiko;
use App\Models\LevelRisiko;
use App\Models\TopRisiko;
use App\Models\TopUnitKerja;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TopRiskRepository
{
    //[cite: 1] Mengambil data top risiko dengan filter dan pagination.
    public function getPaginatedRisks(string $search, int $kategoriId, int $unitId, string $statusAktif): LengthAwarePaginator
    {
        return TopRisiko::query()
            ->with([
                'kategori',
                'unitKerja',
                'monitoringBulanan' => function ($query): void {
                    $query->with(['level', 'aturanEfektivitas'])
                          ->orderByDesc('tahun')
                          ->orderByDesc('bulan');
                },
            ])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('nama_peristiwa_risiko', 'like', "%{$search}%");
            })
            ->when($kategoriId > 0, function ($query) use ($kategoriId): void {
                $query->where('id_kategori', $kategoriId);
            })
            ->when($unitId > 0, function ($query) use ($unitId): void {
                $query->whereHas('unitKerja', function ($unitQuery) use ($unitId): void {
                    $unitQuery->where('top_unit_kerja.id_unit', $unitId);
                });
            })
            ->when($statusAktif !== '', function ($query) use ($statusAktif): void {
                $query->where('is_aktif', $statusAktif === 'aktif');
            })
            ->orderBy('nama_peristiwa_risiko', 'asc')
            ->paginate(10)
            ->withQueryString();
    }

    //[cite: 1] Mengambil daftar kategori yang diurutkan.
    public function getAllKategoriRisiko(): Collection
    {
        return KategoriRisiko::query()->orderBy('nama_kategori', 'asc')->get();
    }

    //[cite: 1] Mengambil daftar unit kerja yang diurutkan.
    public function getAllUnitKerja(): Collection
    {
        return TopUnitKerja::query()->orderBy('nama_unit', 'asc')->get();
    }

    //[cite: 1] Mengambil nilai top risiko yang aktif berserta monitoring terbarunya.
    public function getActiveTopRiskValues(): Collection
    {
        return TopRisiko::query()
            ->with([
                'monitoringBulanan' => function ($query): void {
                    $query->with('level')->orderByDesc('tahun')->orderByDesc('bulan');
                },
            ])
            ->where('is_aktif', true)
            ->orderBy('nama_peristiwa_risiko', 'asc')
            ->get();
    }

    //[cite: 1] Mengambil data level risiko yang diurutkan.
    public function getAllLevelRisiko(): Collection
    {
        return LevelRisiko::query()->orderBy('urutan', 'asc')->get();
    }
}
