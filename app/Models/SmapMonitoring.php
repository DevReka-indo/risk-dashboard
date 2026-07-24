<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SmapMonitoring extends Model
{
    protected $table = 'smap_monitoring';

    protected $primaryKey = 'id_smap';

    // 1. TAMBAHKAN inherent_target DAN id_level_target KE FILLABLE AGAR DATA TIDAK DI-BLOCK LARAVEL
    protected $fillable = [
        'parent_id',
        'id_period',
        'id_unit',
        'id_kategori',
        'id_level',
        'id_level_target', // Tambahkan ini
        'risk_event_deta',
        'value',
        'inherent',
        'inherent_target', // Tambahkan ini
        'trend',
        'status',
    ];

    // 2. TAMBAHKAN CASTS UNTUK INHERENT TARGET
    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'value' => 'integer',
            'inherent' => 'integer',
            'inherent_target' => 'integer', // Tambahkan ini
        ];
    }

    public function getLevelColorClass(): string
    {
        return match (strtolower($this->levelRisiko?->nama_level ?? '')) {
            'high' => 'bg-rose-100 text-rose-700',
            'moderate to high' => 'bg-red-100 text-red-600',
            'moderate' => 'bg-orange-100 text-orange-700',
            'low to moderate', 'medium' => 'bg-amber-100 text-amber-700',
            'low' => 'bg-emerald-100 text-emerald-700',
            default => 'bg-slate-100 text-slate-600',
        };
    }

    public function getTrendColorClass(): string
    {
        return match ($this->trend) {
            'Naik' => 'text-rose-600',
            'Turun' => 'text-emerald-600',
            'Stabil' => 'text-amber-600',
            default => 'text-slate-600',
        };
    }

    public function getTrendIcon(): string
    {
        return match ($this->trend) {
            'Naik' => '▲',
            'Turun' => '▼',
            'Stabil' => '■',
            default => '',
        };
    }

    // === RELASI MASTER DATA (BAWAAN KAMU) ===

    public function kategoriRisiko(): BelongsTo
    {
        return $this->belongsTo(KategoriRisiko::class, 'id_kategori', 'id_kategori');
    }

    public function levelRisiko(): BelongsTo
    {
        return $this->belongsTo(LevelRisiko::class, 'id_level', 'id_level');
    }

    // 3. DAFTARKAN RELASI BARU UNTUK LEVEL TARGET
    public function levelTarget(): BelongsTo
    {
        return $this->belongsTo(LevelRisiko::class, 'id_level_target', 'id_level');
    }

    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(TopUnitKerja::class, 'id_unit', 'id_unit');
    }

    // === TAMBAHAN RELASI UNTUK MONITORING PERIODIK ===

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class, 'id_period', 'id_period');
    }

    public function latestPeriode(): HasOne
    {
        return $this->hasOne(SmapMonitoringPeriod::class, 'id_smap', 'id_smap')
                    ->latestOfMany('id_detail');
    }

    public function detailPeriode(): HasMany
    {
        return $this->hasMany(SmapMonitoringPeriod::class, 'id_smap', 'id_smap')
                    ->latest('id_detail');
    }

    public function scopeParentRisks($query)
    {
        return $query->where('parent_id', null);
    }
}
