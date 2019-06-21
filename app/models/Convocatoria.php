<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Convocatoria extends Model
{
    
    protected $fillable = ["numero_de_convocatoria", "periodo", "codigo_de_postulacion", "oficina_id"];

}
