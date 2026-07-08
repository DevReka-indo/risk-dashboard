<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriRisiko extends Model
{
    protected $table = 'kategori_risiko';

    protected $primaryKey = 'id_kategori';

    protected $fillable = [
        'nama_kategori',
        'type',
        'keterangan',
    ];

    /**
     * Relasi Baru: Ke tabel dep_monitoring
     */
    public function depMonitorings(): HasMany
    {
        return $this->hasMany(DepMonitoring::class, 'id_kategori', 'id_kategori');
    }

    /**
     * Relasi Baru: Ke tabel smap_monitoring
     */
    public function smapMonitorings(): HasMany
    {
        return $this->hasMany(SmapMonitoring::class, 'id_kategori', 'id_kategori');
    }

    /**
     * Relasi Lama: Tetap dipertahankan dari TopKategoriRisiko
     */
    public function risiko(): HasMany
    {
        return $this->hasMany(
            TopRisiko::class,
            'id_kategori',
            'id_kategori'
        );
    }
}
