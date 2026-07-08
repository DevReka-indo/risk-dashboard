<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepMonitoring extends Model
{
    protected $table = 'dep_monitoring';

    protected $primaryKey = 'id_monitoring';

    protected $fillable = [
        'id_unit',
        'id_kategori',
        'id_level',
        'risk_event_deta',
        'value',
        'inherent',
        'trend',
        'status',
        'type',
    ];

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
        // Menyesuaikan dengan relasi 'levelRisiko' dan properti 'nama_level'
        return match ($this->levelRisiko?->nama_level) {
            'Low' => 'bg-emerald-100 text-emerald-700',
            'Low to Moderate' => 'bg-yellow-100 text-yellow-700',
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
            'Naik' => 'text-emerald-600',
            'Turun' => 'text-rose-600',
            'Stabil' => 'text-slate-500',
            default => 'text-slate-400',
        };
    }

    public function kategoriRisiko(): BelongsTo
    {
        return $this->belongsTo(KategoriRisiko::class, 'id_kategori', 'id_kategori');
    }

    public function levelRisiko(): BelongsTo
    {
        return $this->belongsTo(LevelRisiko::class, 'id_level', 'id_level');
    }

    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(TopUnitKerja::class, 'id_unit', 'id_unit');
    }
}
