<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Retencion extends Model
{
    
    protected $fillable = [
        "cronograma_id", "work_id", "cargo_id",
        "mes", "año", "monto"
    ];

}
