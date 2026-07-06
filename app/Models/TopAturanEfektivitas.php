<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TopAturanEfektivitas extends Model
{
    protected $table = 'top_aturan_efektivitas';

    protected $primaryKey = 'id_aturan';

    protected $fillable = [
        'kondisi_nilai',
        'kondisi_level',
        'hasil',
    ];

    public function monitoringBulanan(): HasMany
    {
        return $this->hasMany(
            TopMonitoringBulanan::class,
            'id_aturan_efektivitas',
            'id_aturan'
        );
    }
}
