<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Obligacion extends Model
{
    
    protected $fillable = [
        "beneficiario", "numero_de_documento", "numero_de_cuenta", "monto", "work_id"
    ];

}
