<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopRisikoUnitKerja extends Model
{
    protected $table = 'top_risiko_unit_kerja';

    protected $primaryKey = 'id_risiko_unit';

    protected $fillable = [
        'id_risiko',
        'id_unit',
    ];

    public function risiko(): BelongsTo
    {
        return $this->belongsTo(
            TopRisiko::class,
            'id_risiko',
            'id_risiko'
        );
    }

    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(
            TopUnitKerja::class,
            'id_unit',
            'id_unit'
        );
    }
}
