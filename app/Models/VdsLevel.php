<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VdsLevel extends Model
{
    protected $table = 'vds_level';

    protected $primaryKey = 'id_level';

    protected $fillable = [
        'level_name',
    ];
}
