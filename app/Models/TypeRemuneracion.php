<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeRemuneracion extends Model
{
    
    protected $fillable = [
        "key", "descripcion", "monto", "base"
    ];

}