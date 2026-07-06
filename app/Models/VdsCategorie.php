<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VdsCategorie extends Model
{
    protected $table = 'vds_categorie';

    protected $primaryKey = 'id_category';

    protected $fillable = [
        'category_name',
    ];
}
