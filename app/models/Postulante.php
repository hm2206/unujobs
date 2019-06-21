<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postulante extends Model
{
    
    protected $fillable = [
        "ape_paterno", "ape_materno", "nombres", "numero_de_documento",
        "departamento", "provincia", "distrito", "fecha_de_nacimiento",
        "phone", "email"
    ];

}
