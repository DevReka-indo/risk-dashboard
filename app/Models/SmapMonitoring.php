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

    // Tambahkan parent_id dan id_period di sini agar bisa disimpan lewat Controller
    protected $fillable = [
        'parent_id',
        'id_period',
        'id_unit',
        'id_kategori',
        'id_level',
        'risk_event_deta',
        'value',
        'inherent',
        'trend',
        'status',
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

    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(TopUnitKerja::class, 'id_unit', 'id_unit');
    }

    // === TAMBAHAN RELASI UNTUK MONITORING PERIODIK ===

    // Menghubungkan baris monitoring ke master tabel periode
    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class, 'id_period', 'id_period');
    }

// Di dalam file app/Models/SmapMonitoring.php

// 1. Mengambil hanya 1 data kuartal TERBARU untuk halaman INDEX utama
public function latestPeriode(): HasOne
{
    // Menggunakan 'id_detail' karena itu primary key increment tabel smap_monitoring_periods kita
    return $this->hasOne(SmapMonitoringPeriod::class, 'id_smap', 'id_smap')
                ->latestOfMany('id_detail');
}

// 2. Mengambil semua riwayat kuartal milik risiko ini untuk halaman SHOW detail
public function detailPeriode(): HasMany
{
    return $this->hasMany(SmapMonitoringPeriod::class, 'id_smap', 'id_smap')
                ->latest('id_detail');
}
}
