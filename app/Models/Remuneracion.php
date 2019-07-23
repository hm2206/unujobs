<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Remuneracion extends Model
{

    protected $fillable = [
        "work_id", "categoria_id", "dias", "cronograma_id",
        "observaciones", "sede_id", "mes", "año", "monto", "adicional",
        "horas", "type_remuneracion_id", 'cargo_id', 'base'
    ];
    
    public function concepto()
    {
        return $this->belongsTo(Concepto::class);
    }

    public function typeRemuneracion()
    {
        return $this->belongsTo(TypeRemuneracion::class);
    }

}
