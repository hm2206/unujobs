<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postulante extends Model
{
    
    protected $fillable = [
        "ape_paterno", "ape_materno", "nombres", "numero_de_documento",
        "departamento_id", "provincia_id", "distrito_id", "fecha_de_nacimiento",
        "phone", "email", 'cv', 'nombre_completo'
    ];

    public function etapas()
    {
        return $this->hasMany(Etapa::class);
    }

}
