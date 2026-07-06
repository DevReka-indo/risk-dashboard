<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VsmapMonitoring extends Model
{
    protected $table = 'vsmap_monitoring';

    protected $primaryKey = 'id_smap';

    // Matikan $timestamps karena tabel ini hanya memakai 'created_at' tanpa 'updated_at'
    public $timestamps = false;

    protected $fillable = [
        'id_unit',
        'id_category',
        'id_level',
        'risk_event',
        'value',
        'inherent',
        'trend',
        'status',
        'created_at',
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
