<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Descuento extends Model
{
    
    protected $fillable = [
        "work_id", "categoria_id", "dias", "cronograma_id",
        "observaciones", "sede_id", "mes", "año", "monto", "adicional",
        "horas", "type_descuento_id", "cargo_id"
    ];

    public function typeDescuento()
    {
        return $this->belongsTo(TypeDescuento::class, 'type_descuento_id');
    }

}
