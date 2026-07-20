<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AuditLogExport;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        // 1. Inisialisasi Query untuk Audit Trail (urut dari yang terbaru)
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        // 2. Logika Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('aktivitas', 'like', "%{$search}%")
                  ->orWhere('modul', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 3. Logika Filter Rentang Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // 4. Perhitungan Statistik Kartu
        $totalLogs = AuditLog::count();
        $todayLogs = AuditLog::whereDate('created_at', Carbon::today())->count();
        $activeUsers = AuditLog::whereNotNull('user_id')->distinct('user_id')->count('user_id');

        // 5. Eksekusi Query dan Pagination
        $logs = $query->paginate(10)->withQueryString();

        // (Opsional) Di sini nantinya Anda bisa memanggil data settings lain
        // $settings = Setting::all();

        // 6. Lempar semua data ke View
        return view('settings.index', compact('logs', 'totalLogs', 'todayLogs', 'activeUsers'));
    }

    public function updateSystem(Request $request)
    {
        $request->validate([
            'app_name'     => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'default_year' => 'required|numeric',
        ]);

        // TODO: Simpan ke database

        return redirect()->route('settings.index')->with('success', 'Konfigurasi Sistem berhasil diperbarui!');
    }

    public function export(Request $request)
    {
        // Generate nama file dinamis beserta tanggal
        $fileName = 'Audit_Log_' . now()->format('Y-m-d_H-i') . '.xlsx';

        // Panggil class Export dan teruskan $request agar filter terbaca
        return Excel::download(new AuditLogExport($request), $fileName);
    }

    public function clearAudit()
    {
        // Menghapus seluruh data di tabel audit_logs
        AuditLog::truncate();

        // Redirect kembali ke halaman setting dengan tab audit aktif
        return redirect()->route('settings.index', ['tab' => 'audit'])
                         ->with('success', 'Semua riwayat audit berhasil dihapus.');
    }
}
