<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopMonitoringBulanan extends Model
{
    protected $table = 'top_monitoring_bulanan';

    protected $primaryKey = 'id_monitoring';

    protected $fillable = [
        'id_risiko',
        'bulan',
        'tahun',
        'nilai',
        'id_level',
        'status',
        'progres_belum',
        'progres_proses',
        'progres_sudah',
        'id_aturan_efektivitas',
        'catatan',
    ];

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'nilai' => 'integer',
        'progres_belum' => 'integer',
        'progres_proses' => 'integer',
        'progres_sudah' => 'integer',
    ];

    public function risiko(): BelongsTo
    {
        return $this->belongsTo(
            TopRisiko::class,
            'id_risiko',
            'id_risiko'
        );
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(
            LevelRisiko::class,
            'id_level',
            'id_level'
        );
    }

    public function aturanEfektivitas(): BelongsTo
    {
        return $this->belongsTo(
            TopAturanEfektivitas::class,
            'id_aturan_efektivitas',
            'id_aturan'
        );
    }

    public function getHasilEfektivitasAttribute(): ?string
    {
        return $this->aturanEfektivitas?->hasil;
    }
}
