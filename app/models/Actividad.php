<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    
    protected $fillable = [
        "descripcion", "fecha_inicio", "fecha_final", "responsable", "convocatoria_id"
    ];

}
