<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeAfp extends Model
{
    
    protected $fillable = [
        "descripcion", "type_descuento_id"
    ];


    public function afps()
    {
        return $this->belongsToMany(Afp::class, 'afp_type_afp')
            ->withPivot(['porcentaje']);
    }

    public function type_descuento()
    {
        return $this->belongsTo(TypeDescuento::class);
    }

}
