<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TopUnitKerja extends Model
{
    protected $table = 'top_unit_kerja';

    protected $primaryKey = 'id_unit';

    protected $fillable = [
        'nama_unit',
        'keterangan',
    ];

    public function risiko(): BelongsToMany
    {
        return $this->belongsToMany(
            TopRisiko::class,
            'top_risiko_unit_kerja',
            'id_unit',
            'id_risiko'
        )
            ->withPivot('id_risiko_unit')
            ->withTimestamps();
    }
}
