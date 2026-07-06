<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TopKategoriRisiko extends Model
{
    protected $table = 'top_kategori_risiko';

    protected $primaryKey = 'id_kategori';

    protected $fillable = [
        'nama_kategori',
        'keterangan',
    ];

    public function risiko(): HasMany
    {
        return $this->hasMany(
            TopRisiko::class,
            'id_kategori',
            'id_kategori'
        );
    }
}
