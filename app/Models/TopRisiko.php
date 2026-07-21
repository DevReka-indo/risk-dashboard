<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TopRisiko extends Model
{
    protected $table = 'top_risiko';

    protected $primaryKey = 'id_risiko';

    protected $fillable = [
        'nama_peristiwa_risiko',
        'id_kategori',
        'tanggal_dibuat',
        'is_aktif',
        'inherent',
        'target_tw1',
        'target_tw2',
        'target_tw3',
        'target_tw4',
    ];

    protected $casts = [
        'tanggal_dibuat' => 'date',
        'is_aktif'       => 'boolean',
        'inherent'       => 'integer',
        'target_tw1'     => 'integer',
        'target_tw2'     => 'integer',
        'target_tw3'     => 'integer',
        'target_tw4'     => 'integer',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(
            KategoriRisiko::class,
            'id_kategori',
            'id_kategori'
        );
    }

    public function unitKerja(): BelongsToMany
    {
        return $this->belongsToMany(
            TopUnitKerja::class,
            'top_risiko_unit_kerja',
            'id_risiko',
            'id_unit'
        )
            ->withPivot('id_risiko_unit')
            ->withTimestamps();
    }

    public function monitoringBulanan(): HasMany
    {
        return $this->hasMany(
            TopMonitoringBulanan::class,
            'id_risiko',
            'id_risiko'
        );
    }

    public function monitoringTerbaru(): HasMany
    {
        return $this->hasMany(
            TopMonitoringBulanan::class,
            'id_risiko',
            'id_risiko'
        )->latest('tahun')->latest('bulan');
    }
}
