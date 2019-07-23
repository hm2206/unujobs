<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etapa extends Model
{
    
    protected $fillable = [
        "postulante_id", "type_etapa_id", "convocatoria_id",
        "personal_id", "current", "next", 'puntaje'
    ];

}
