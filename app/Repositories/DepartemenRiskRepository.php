<?php

namespace App\Repositories;

use App\Models\DepMonitoring;
use App\Models\TopUnitKerja;
use App\Models\KategoriRisiko;
use App\Models\LevelRisiko;
use Illuminate\Support\Facades\DB;

class DepartemenRiskRepository
{
    /**
     * Ambil data risiko dengan fitur filter dan pagination (Untuk Tab Data)
     */
    public function getPaginatedRisks(array $filters = [], int $perPage = 10)
    {
        return DepMonitoring::query()
            ->with(['unitKerja', 'kategoriRisiko', 'levelRisiko', 'periods' => function($q) {
                $q->orderBy('year', 'desc')->orderBy('quarter', 'desc');
            }])
            ->when(!empty($filters['search']), fn($q) => $q->where('risk_event_deta', 'like', '%' . $filters['search'] . '%'))
            ->when(!empty($filters['unit_id']), fn($q) => $q->where('id_unit', $filters['unit_id']))
            ->when(!empty($filters['category_id']), fn($q) => $q->where('id_kategori', $filters['category_id']))
            ->when(!empty($filters['level_id']), fn($q) => $q->where('id_level', $filters['level_id']))
            ->when(!empty($filters['type']), fn($q) => $q->where('type', $filters['type']))
            ->when(isset($filters['status']) && $filters['status'] !== '', fn($q) => $q->where('status', (bool) $filters['status']))
            ->oldest('id_monitoring')
            ->paginate($perPage)
            ->withQueryString();
    }

    // --- DATA MASTER (Untuk Dropdown Form) ---
    public function getAllUnits() {
        return TopUnitKerja::orderBy('nama_unit', 'asc')->get();
    }
    public function getAllCategories() {
        return KategoriRisiko::orderBy('nama_kategori', 'asc')->get();
    }
    public function getAllLevels() {
        return LevelRisiko::orderBy('id_level', 'asc')->get();
    }
    public function getLevelsOrdered() {
        return LevelRisiko::orderBy('urutan', 'asc')->get();
    }
    public function findLevelByIdOrName($levelInput) {
        return LevelRisiko::where('id_level', $levelInput)->orWhere('nama_level', $levelInput)->first();
    }

    // --- CRUD RISIKO UTAMA ---
    public function findById(string $id) {
        return DepMonitoring::with(['unitKerja', 'kategoriRisiko', 'periods' => function($q) {
            $q->orderBy('year', 'desc')->orderBy('quarter', 'desc');
        }])->findOrFail($id);
    }

    public function create(array $data) {
        return DepMonitoring::create($data);
    }

    public function update(string $id, array $data) {
        $risk = DepMonitoring::findOrFail($id);
        $risk->update($data);
        return $risk;
    }

    public function delete(string $id) {
        return DepMonitoring::findOrFail($id)->delete();
    }

    // --- MANAJEMEN PERIODE (TRIWULAN) ---
    public function checkPeriodExists(string $riskId, string $quarter, int $year) {
        return DB::table('dep_monitoring_periods')
            ->where('id_monitoring', $riskId)
            ->where('quarter', $quarter)
            ->where('year', $year)
            ->exists();
    }

    public function getPeriodData(string $riskId, string $quarter, int $year) {
        return DB::table('dep_monitoring_periods')
            ->where('id_monitoring', $riskId)
            ->where('year', $year)
            ->where('quarter', $quarter)
            ->first();
    }

    public function getPivotById(string $pivotId) {
        return DB::table('dep_monitoring_periods')->where('id', $pivotId)->first();
    }

    public function attachPeriod(string $riskId, $idLevelTerbaru, array $pivotData) {
        $risk = DepMonitoring::findOrFail($riskId);
        $risk->periods()->attach($idLevelTerbaru, $pivotData);
        return $risk;
    }

    public function updatePivotPeriod(string $pivotId, array $data) {
        return DB::table('dep_monitoring_periods')->where('id', $pivotId)->update($data);
    }

    public function deletePivotPeriod(string $pivotId) {
        return DB::table('dep_monitoring_periods')->where('id', $pivotId)->delete();
    }
}
