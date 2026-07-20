<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DepMonitoring;
use App\Models\SmapMonitoring;
use App\Models\TopRisiko;
use App\Models\KategoriRisiko;
use App\Models\LevelRisiko;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        // 1. Dapatkan ID Level untuk kategori bahaya (High & Moderate to High)
        $highLevelIds = LevelRisiko::whereIn('nama_level', ['High', 'Tinggi', 'Moderate to High'])
            ->pluck('id_level')
            ->toArray();

        // 2. Kumpulkan Statistik KPI
        $stats = [
            'total_risks' => DepMonitoring::count() + SmapMonitoring::whereNull('parent_id')->count() + TopRisiko::count(),

            // Total Risiko Kritis (Merah)
            'high_risks' => DepMonitoring::whereIn('id_level', $highLevelIds)->count()
                          + SmapMonitoring::whereIn('id_level', $highLevelIds)->whereNull('parent_id')->count(),

            // Menunggu Tindakan (Belum atau Proses)
            // Menunggu Tindakan (Total akumulasi angka progres Belum + Proses)
            'pending_actions' => DB::table('dep_monitoring_periods')->sum('progres_belum')
                            + DB::table('dep_monitoring_periods')->sum('progres_proses'),

            // Rincian per Modul
            'total_dep' => DepMonitoring::count(),
            'total_smap' => SmapMonitoring::whereNull('parent_id')->count(),
            'total_top' => TopRisiko::count(),
        ];

        // 3. Data Distribusi Level Risiko (Untuk Donut Chart)
        $levelDistribution = DB::table('level_risiko')
            ->leftJoin('dep_monitoring', 'level_risiko.id_level', '=', 'dep_monitoring.id_level')
            ->select('level_risiko.nama_level', DB::raw('count(dep_monitoring.id_monitoring) as total'))
            ->groupBy('level_risiko.id_level', 'level_risiko.nama_level')
            ->pluck('total', 'nama_level')
            ->toArray();

        // 4. Data Kategori Teratas (Untuk Bar Chart)
        $riskCategories = KategoriRisiko::withCount('depMonitorings')
            ->having('dep_monitorings_count', '>', 0)
            ->orderByDesc('dep_monitorings_count')
            ->take(6) // Ambil 6 besar agar grafik rapi
            ->get()
            ->map(fn ($cat) => [
                'name' => $cat->nama_kategori,
                'total' => $cat->dep_monitorings_count,
            ]);

        // 5. Tabel Top 5 High Risks (Fokus Perhatian)
        $topHighRisks = DepMonitoring::with(['unitKerja', 'levelRisiko'])
            ->whereIn('id_level', $highLevelIds)
            ->orderByDesc('value') // Urutkan dari skor tertinggi
            ->take(5)
            ->get();

        // 6. Tabel Update Terakhir (Log Aktivitas)
        $recentUpdates = DepMonitoring::with(['unitKerja'])
            ->orderByDesc('updated_at') // Urutkan dari yang paling baru diupdate
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'stats',
            'levelDistribution',
            'riskCategories',
            'topHighRisks',
            'recentUpdates'
        ));
    }
}
