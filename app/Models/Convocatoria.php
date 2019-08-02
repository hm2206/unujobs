<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Convocatoria extends Model
{
    
    protected $fillable = [
        "numero_de_convocatoria", "observacion", "fecha_inicio", "fecha_final",
        "aceptado"
    ];

    public function personals()
    {
        return $this->hasMany(Personal::class);
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class);
    }


    public function slug()
    {
        return \base64_encode($this->id);
    }

}
