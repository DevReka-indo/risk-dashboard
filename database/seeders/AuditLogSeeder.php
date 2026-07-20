<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AuditLog;
use Carbon\Carbon;

class AuditLogSeeder extends Seeder
{
    public function run()
    {
        AuditLog::insert([
            [
                'user_id' => 1, // Pastikan ada user dengan ID 1 di tabel users Anda
                'modul' => 'Top Risk',
                'aktivitas' => 'Mengubah nilai risiko <strong>"Keterlambatan Material"</strong> dari 15 ke 20.',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'user_id' => 1,
                'modul' => 'Monitoring',
                'aktivitas' => 'Mengisi progress penanganan bulan <strong>Juli 2026</strong>.',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'user_id' => 1,
                'modul' => 'Departemen',
                'aktivitas' => 'Menambahkan master risiko baru <strong>"Fluktuasi Nilai Tukar"</strong>.',
                'created_at' => Carbon::now(), // Aktivitas hari ini
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
