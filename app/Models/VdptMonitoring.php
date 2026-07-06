<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VdptMonitoring extends Model
{
    protected $table = 'vdpt_monitoring';

    protected $primaryKey = 'id_monitoring';

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'value' => 'integer',
            'inherent' => 'integer',
        ];
    }

    public function getLevelColorClass(): string
    {
        return match ($this->level?->level_name) {
            'Low' => 'bg-emerald-100 text-emerald-700',
            'Low To Moderate' => 'bg-yellow-100 text-yellow-700',
            'Moderate' => 'bg-orange-100 text-orange-700',
            'Moderate to High' => 'bg-red-100 text-red-600',
            'High' => 'bg-rose-100 text-rose-700',
            default => 'bg-slate-100 text-slate-600',
        };
    }

    public function getTrendIcon(): string
    {
        return match ($this->trend) {
            'Naik' => '↑',
            'Turun' => '↓',
            'Stabil' => '→',
            default => '–',
        };
    }

    public function getTrendColorClass(): string
    {
        return match ($this->trend) {
            'Naik' => 'text-emerald-600 ',
            'Turun' => 'text-rose-600',
            'Stabil' => 'text-slate-500',
            default => 'text-slate-400',
        };
    }

    protected $fillable = [
        'id_unit',
        'id_category',
        'id_level',
        'risk_event_deta',
        'value',
        'inherent',
        'trend',
        'status',
        'type',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(TopUnitKerja::class, 'id_unit', 'id_unit');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(VdsCategorie::class, 'id_category', 'id_category');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(VdsLevel::class, 'id_level', 'id_level');
    }
}
