<?php

namespace App\Repositories;

use App\Models\SmapMonitoring;
use App\Models\SmapMonitoringPeriod;
use App\Models\TopUnitKerja;
use App\Models\LevelRisiko;
use App\Models\KategoriRisiko;
use Illuminate\Support\Facades\DB;

class SmapRepository
{
    public function getAllUnits()
    {
        return TopUnitKerja::orderBy('nama_unit', 'asc')->get();
    }

    public function getAllLevels()
    {
        return LevelRisiko::orderBy('id_level', 'asc')->get();
    }

    public function getSmapCategories()
    {
        return KategoriRisiko::query()->where('type', 'smap')->orderBy('nama_kategori', 'asc')->get();
    }

    public function getAllCategories()
    {
        return KategoriRisiko::orderBy('nama_kategori', 'asc')->get();
    }

    public function getLatestPeriodYear()
    {
        return SmapMonitoringPeriod::latest('year')->first();
    }

    public function getDashboardMetrics(array $quarterLookups, int $selectedYear)
    {
        $totalRisiko = DB::table('smap_monitoring_periods')
            ->whereIn('quarter', $quarterLookups)
            ->where('year', $selectedYear)
            ->count();

        $risikoAktif = DB::table('smap_monitoring_periods')
            ->join('smap_monitoring', 'smap_monitoring_periods.id_smap', '=', 'smap_monitoring.id_smap')
            ->whereIn('smap_monitoring_periods.quarter', $quarterLookups)
            ->where('year', $selectedYear)
            ->where('smap_monitoring.status', 1)
            ->count();

        $risksPerDept = DB::table('smap_monitoring_periods')
            ->join('smap_monitoring', 'smap_monitoring_periods.id_smap', '=', 'smap_monitoring.id_smap')
            ->whereIn('smap_monitoring_periods.quarter', $quarterLookups)
            ->where('year', $selectedYear)
            ->selectRaw('smap_monitoring.id_unit, count(*) as total')
            ->groupBy('smap_monitoring.id_unit')
            ->pluck('total', 'id_unit')
            ->toArray();

        $risksPerLevel = DB::table('smap_monitoring_periods')
            ->whereIn('quarter', $quarterLookups)
            ->where('year', $selectedYear)
            ->selectRaw('id_level, count(*) as total')
            ->groupBy('id_level')
            ->pluck('total', 'id_level')
            ->toArray();

        $risksPerCategory = DB::table('smap_monitoring_periods')
            ->join('smap_monitoring', 'smap_monitoring_periods.id_smap', '=', 'smap_monitoring.id_smap')
            ->whereIn('smap_monitoring_periods.quarter', $quarterLookups)
            ->where('year', $selectedYear)
            ->selectRaw('smap_monitoring.id_kategori, count(*) as total')
            ->groupBy('smap_monitoring.id_kategori')
            ->pluck('total', 'id_kategori')
            ->toArray();

        $risksPerTrend = DB::table('smap_monitoring_periods')
            ->whereIn('quarter', $quarterLookups)
            ->where('year', $selectedYear)
            ->selectRaw('trend, count(*) as total')
            ->groupBy('trend')
            ->pluck('total', 'trend')
            ->toArray();

        return compact('totalRisiko', 'risikoAktif', 'risksPerDept', 'risksPerLevel', 'risksPerCategory', 'risksPerTrend');
    }

    public function getDeptRisksPerLevel(array $quarterLookups, int $selectedYear, int $unitId)
    {
        return DB::table('smap_monitoring_periods')
            ->join('smap_monitoring', 'smap_monitoring_periods.id_smap', '=', 'smap_monitoring.id_smap')
            ->whereIn('smap_monitoring_periods.quarter', $quarterLookups)
            ->where('year', $selectedYear)
            ->where('smap_monitoring.id_unit', $unitId)
            ->selectRaw('smap_monitoring_periods.id_level, count(*) as total')
            ->groupBy('smap_monitoring_periods.id_level')
            ->pluck('total', 'id_level')
            ->toArray();
    }

    public function getMasterDataCountsByYear(int $selectedYear, string $column = 'id_level')
    {
        return DB::table('smap_monitoring')
            ->whereYear('created_at', $selectedYear)
            ->selectRaw("$column, count(*) as total")
            ->groupBy($column)
            ->pluck('total', $column)
            ->toArray();
    }

    public function getProgresOffData(int $selectedYear, array $quarterLookups)
    {
        return DB::table('smap_monitoring_periods')
            ->where('year', $selectedYear)
            ->whereIn('quarter', $quarterLookups)
            ->selectRaw('status_penanganan, count(*) as total')
            ->groupBy('status_penanganan')
            ->pluck('total', 'status_penanganan')
            ->toArray();
    }

    public function getEfektifOffData(int $selectedYear, array $quarterLookups)
    {
        return DB::table('smap_monitoring_periods')
            ->where('year', $selectedYear)
            ->whereIn('quarter', $quarterLookups)
            ->selectRaw('efektif_risiko, count(*) as total')
            ->groupBy('efektif_risiko')
            ->pluck('total', 'efektif_risiko')
            ->toArray();
    }

    public function getPaginatedRisks(array $filters, int $perPage = 10)
    {
        return SmapMonitoring::query()
            ->where('parent_id', null)
            ->with(['unitKerja', 'kategoriRisiko', 'levelRisiko', 'latestPeriode.period'])
            ->when($filters['search'], function ($query, $search) {
                $query->where('risk_event_deta', 'like', "%{$search}%");
            })
            ->when($filters['unit_id'], function ($query, $unitId) {
                $query->where('id_unit', $unitId);
            })
            ->when($filters['category_id'], function ($query, $categoryId) {
                $query->where('id_kategori', $categoryId);
            })
            ->when($filters['level_id'], function ($query, $levelId) {
                $query->where('id_level', $levelId);
            })
            ->when($filters['trend'], function ($query, $trend) {
                $query->where('trend', $trend);
            })
            ->when($filters['status'] !== '', function ($query) use ($filters) {
                $query->where('status', (bool) $filters['status']);
            })
            // UBAH BAGIAN INI: Dari oldest('id_smap') menjadi latest('created_at')
            ->latest('created_at')
            ->paginate($perPage);
    }
}
