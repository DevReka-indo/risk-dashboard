<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TopLevelRisiko extends Model
{
    protected $table = 'top_level_risiko';

    protected $primaryKey = 'id_level';

    protected $fillable = [
        'nama_level',
        'urutan',
        'kode_warna',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    public function monitoringBulanan(): HasMany
    {
        return $this->hasMany(
            TopMonitoringBulanan::class,
            'id_level',
            'id_level'
        );
    }
}
