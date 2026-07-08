<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Period extends Model
{
    use HasFactory;

    // Menentukan nama tabel di database secara eksplisit
    protected $table = 'periods';

    // Menentukan Primary Key sesuai gambar rancangan Anda
    protected $primaryKey = 'id_period';

    // Daftarkan kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'period_name',
        'year',
        'quarter',
        'value',       // Tambahkan ini
        'inherent',    // Tambahkan ini
        'id_smap',     // Pastikan foreign key ini ada
    ];

    /**
     * Hubungkan relasi ke tabel monitoring departemen (One to Many)
     */
    public function depMonitorings(): HasMany
    {
        return $this->hasMany(DepMonitoring::class, 'id_period', 'id_period');
    }

    /**
     * Hubungkan relasi ke tabel monitoring SMAP (One to Many)
     */
    public function smapMonitorings(): HasMany
    {
        return $this->hasMany(SmapMonitoring::class, 'id_period', 'id_period');
    }
}
