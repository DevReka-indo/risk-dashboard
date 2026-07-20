<?php

namespace App\Helpers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class LogHelper
{
    /**
     * Mencatat aktivitas user ke dalam tabel audit_logs
     *
     * @param string $modul
     * @param string $aktivitas
     * @return void
     */
    public static function aktivitas($modul, $aktivitas)
    {
        AuditLog::create([
            'user_id'   => Auth::id(), // Mengambil ID user yang sedang login
            'modul'     => $modul,
            'aktivitas' => $aktivitas,
        ]);
    }
}
