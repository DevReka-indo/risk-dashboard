<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LevelRisiko extends Model
{
    protected $table = 'level_risiko';

    protected $primaryKey = 'id_level';

    protected $fillable = [
        'nama_level',
        'urutan',
        'kode_warna',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    /**
     * Relasi Baru: Ke tabel dep_monitoring
     */
    public function depMonitorings(): HasMany
    {
        return $this->hasMany(DepMonitoring::class, 'id_level', 'id_level');
    }

    /**
     * Relasi Baru: Ke tabel smap_monitoring
     */
    public function smapMonitorings(): HasMany
    {
        return $this->hasMany(SmapMonitoring::class, 'id_level', 'id_level');
    }

    /**
     * Relasi Lama: Tetap dipertahankan agar fitur bulanan tidak rusak
     */
    public function monitoringBulanan(): HasMany
    {
        return $this->hasMany(
            TopMonitoringBulanan::class,
            'id_level',
            'id_level'
        );
    }
}
