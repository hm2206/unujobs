<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeAportacion extends Model
{
    
    protected $fillable = [
        'descripcion', 'porcentaje', 'minimo', 'default', 'type_descuento_id'
    ];


    public function type_descuento()
    {
        return $this->belongsTo(TypeDescuento::class);
    }

}
