<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VdptMonitoring;
use App\Models\VsmapMonitoring;
use Illuminate\Http\JsonResponse;

class MonitoringController extends Controller
{
    /**
     * Mengambil semua data DPT Monitoring beserta relasinya.
     */
    public function getDptMonitoring(): JsonResponse
    {
        try {
            // Eager loading relasi master unit, category, dan level
            $data = VdptMonitoring::with(['unit', 'category', 'level'])->get();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil data DPT Monitoring',
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mengambil semua data SMAP Monitoring beserta relasinya.
     */
    public function getSmapMonitoring(): JsonResponse
    {
        try {
            // Eager loading relasi master unit, category, dan level
            $data = VsmapMonitoring::with(['unit', 'category', 'level'])->get();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil data SMAP Monitoring',
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
