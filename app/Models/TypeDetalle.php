<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeDetalle extends Model
{
    
    protected $fillable = ['descripcion', 'type_descuento_id'];

    public function typeDescuento()
    {
        return $this->belongsTo(TypeDescuento::class);
    }

}
