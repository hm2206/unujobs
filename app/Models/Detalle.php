<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detalle extends Model
{
    
    protected $fillable = [
        'type_descuento_id', 'type_detalle_id', 'work_id', 'cronograma_id', 
        'categoria_id', 'monto'
    ];

}
