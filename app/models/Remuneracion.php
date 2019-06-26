<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Remuneracion extends Model
{

    protected $fillable = [
        "job_id", "categoria_id", "dias", "concepto_id", "cronograma_id",
        "observaciones", "sede_id", "mes", "año", "monto", "adicional",
        "horas"
    ];
    
    public function concepto()
    {
        return $this->belongsTo(Concepto::class);
    }

}
