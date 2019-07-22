<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postulante extends Model
{
    
    protected $fillable = [
        "ape_paterno", "ape_materno", "nombres", "numero_de_documento",
        "departamento_id", "provincia_id", "distrito_id", "fecha_de_nacimiento",
        "phone", "email", 'cv'
    ];

    public function personals()
    {
        return $this->belongsToMany(Personal::class, 'personal_postulante')
            ->withPivot(["type_etapa_id"]);
    }

}
