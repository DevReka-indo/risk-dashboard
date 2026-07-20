<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    // Menentukan tabel secara eksplisit (opsional tapi disarankan)
    protected $table = 'audit_logs';

    // Kolom yang diizinkan untuk diisi data
    protected $fillable = [
        'user_id',
        'modul',
        'aktivitas'
    ];

    // Relasi ke tabel User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
