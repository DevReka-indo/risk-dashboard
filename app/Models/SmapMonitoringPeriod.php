<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmapMonitoringPeriod extends Model
{
    protected $table = 'smap_monitoring_periods';
    protected $primaryKey = 'id_detail';

    protected $fillable = [
            'id_smap',
            'quarter',
            'year',
            'value',
            'inherent',
            'inherent_target',
            'id_level',
            'id_level_target',
            'trend',
            'status_penanganan',
            'efektif_risiko',
        ];

    public function monitoring(): BelongsTo
    {
        return $this->belongsTo(SmapMonitoring::class, 'id_smap', 'id_smap');
    }

    public function levelRisiko(): BelongsTo
    {
        return $this->belongsTo(LevelRisiko::class, 'id_level', 'id_level');
    }

    public function period(): BelongsTo
    {

        return $this->belongsTo(Period::class, 'id_period', 'id_period');
    }

    public function getPeriodAttribute()
    {
        $quarterText = [
            'Q1' => 'TW1', 'Q2' => 'TW2', 'Q3' => 'TW3', 'Q4' => 'TW4'
        ][$this->quarter] ?? $this->quarter;

        return (object) [
            'period_name' => $quarterText . ' ' . $this->year
        ];
    }
}
