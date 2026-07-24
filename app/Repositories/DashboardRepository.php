<?php

namespace App\Repositories;

use App\Models\DepMonitoring;
use App\Models\KategoriRisiko;
use App\Models\LevelRisiko;
use App\Models\SmapMonitoring;
use App\Models\TopRisiko;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    public function getHighLevelIds(): array
    {
        return LevelRisiko::whereIn('nama_level', ['High', 'Tinggi', 'Moderate to High'])
            ->pluck('id_level')
            ->toArray();
    }

    public function getKpiStats(array $highLevelIds): array
    {
        return [
            'total_risks' => DepMonitoring::count() + SmapMonitoring::whereNull('parent_id')->count() + TopRisiko::count(),
            'high_risks' => DepMonitoring::whereIn('id_level', $highLevelIds)->count()
                          + SmapMonitoring::whereIn('id_level', $highLevelIds)->whereNull('parent_id')->count(),
            'pending_actions' => DB::table('dep_monitoring_periods')->sum('progres_belum')
                            + DB::table('dep_monitoring_periods')->sum('progres_proses'),
            'total_dep' => DepMonitoring::count(),
            'total_smap' => SmapMonitoring::whereNull('parent_id')->count(),
            'total_top' => TopRisiko::count(),
        ];
    }

    public function getLevelDistribution(): array
    {
        return DB::table('level_risiko')
            ->leftJoin('dep_monitoring', 'level_risiko.id_level', '=', 'dep_monitoring.id_level')
            ->select('level_risiko.nama_level', DB::raw('count(dep_monitoring.id_monitoring) as total'))
            ->groupBy('level_risiko.id_level', 'level_risiko.nama_level')
            ->pluck('total', 'nama_level')
            ->toArray();
    }

    public function getTopRiskCategories(int $limit = 6): Collection
    {
        return KategoriRisiko::withCount('depMonitorings')
            ->having('dep_monitorings_count', '>', 0)
            ->orderByDesc('dep_monitorings_count')
            ->take($limit)
            ->get();
    }

    public function getTopHighRisks(array $highLevelIds, int $limit = 5): Collection
    {
        return DepMonitoring::with(['unitKerja', 'levelRisiko'])
            ->whereIn('id_level', $highLevelIds)
            ->orderByDesc('value')
            ->take($limit)
            ->get();
    }

    public function getRecentUpdates(int $limit = 5): Collection
    {
        return DepMonitoring::with(['unitKerja'])
            ->orderByDesc('updated_at')
            ->take($limit)
            ->get();
    }

    public function getRisksForHeatmap(): Collection
    {
        return DepMonitoring::with(['levelRisiko', 'unitKerja'])
            ->whereNotNull('value')
            ->where('value', '>', 0)
            ->orderByDesc('value')
            ->get();
    }

    public function getSmapKomposisiRaw(): Collection
    {
        return DB::table('smap_monitoring_periods as p')
            ->join('smap_monitoring as m', 'p.id_smap', '=', 'm.id_smap')
            ->join('top_unit_kerja as u', 'm.id_unit', '=', 'u.id_unit')
            ->join('level_risiko as l', 'p.id_level', '=', 'l.id_level')
            ->select(
                'u.nama_unit as unit',
                'l.nama_level as level',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('u.nama_unit', 'l.nama_level')
            ->get();
    }

    public function getSmapUnitTableRaw(): Collection
    {
        return DB::table('top_unit_kerja as u')
            ->leftJoin('smap_monitoring as m', function ($join) {
                $join->on('u.id_unit', '=', 'm.id_unit')
                     ->whereNull('m.parent_id');
            })
            ->leftJoin('smap_monitoring_periods as p', 'm.id_smap', '=', 'p.id_smap')
            ->select(
                'u.nama_unit',
                DB::raw('COALESCE(SUM(p.progress_belum), 0) as belum'),
                DB::raw('COALESCE(SUM(p.progress_proses), 0) as proses'),
                DB::raw('COALESCE(SUM(p.progress_sudah), 0) as sudah')
            )
            ->groupBy('u.id_unit', 'u.nama_unit')
            ->get();
    }

    public function getDepartemenUnitsRaw(): Collection
    {
        return DB::table('top_unit_kerja')
            ->select('top_unit_kerja.nama_unit', DB::raw('COUNT(dep_monitoring.id_monitoring) as total_risiko'))
            ->leftJoin('dep_monitoring', 'top_unit_kerja.id_unit', '=', 'dep_monitoring.id_unit')
            ->groupBy('top_unit_kerja.id_unit', 'top_unit_kerja.nama_unit')
            ->having('total_risiko', '>', 0)
            ->orderBy('total_risiko', 'desc')
            ->get();
    }

    public function getDepartemenProgresPerUnitRaw(): Collection
    {
        return DB::table('top_unit_kerja')
            ->select(
                'top_unit_kerja.nama_unit',
                DB::raw('COALESCE(SUM(dep_monitoring_periods.progres_belum), 0) as belum'),
                DB::raw('COALESCE(SUM(dep_monitoring_periods.progres_proses), 0) as proses'),
                DB::raw('COALESCE(SUM(dep_monitoring_periods.progres_sudah), 0) as sudah')
            )
            ->leftJoin('dep_monitoring', 'top_unit_kerja.id_unit', '=', 'dep_monitoring.id_unit')
            ->leftJoin('dep_monitoring_periods', 'dep_monitoring.id_monitoring', '=', 'dep_monitoring_periods.id_monitoring')
            ->groupBy('top_unit_kerja.id_unit', 'top_unit_kerja.nama_unit')
            ->havingRaw('COALESCE(SUM(dep_monitoring_periods.progres_belum), 0) + COALESCE(SUM(dep_monitoring_periods.progres_proses), 0) + COALESCE(SUM(dep_monitoring_periods.progres_sudah), 0) > 0')
            ->get();
    }
}
