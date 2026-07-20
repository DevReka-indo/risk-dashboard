<?php

namespace App\Exports;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AuditLogExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $request;

    // Menangkap request dari controller
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    // Menggunakan FromQuery agar aman untuk data besar
    public function query()
    {
        $query = AuditLog::query()->with('user')->orderBy('created_at', 'desc');

        // Filter Pencarian
        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function($q) use ($search) {
                $q->where('aktivitas', 'like', "%{$search}%")
                  ->orWhere('modul', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter Tanggal Mulai
        if ($this->request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $this->request->start_date);
        }

        // Filter Tanggal Akhir
        if ($this->request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $this->request->end_date);
        }

        return $query;
    }

    // Menambahkan Header Kolom
    public function headings(): array
    {
        return [
            'Waktu',
            'Pengguna',
            'Modul',
            'Aktivitas'
        ];
    }

    // Memetakan isi baris
    public function map($log): array
    {
        return [
            \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M Y H:i'),
            $log->user ? $log->user->name : 'Sistem',
            $log->modul,
            strip_tags($log->aktivitas)
        ];
    }

    // Styling Header
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF5A5CFA']],
            ],
        ];
    }
}
