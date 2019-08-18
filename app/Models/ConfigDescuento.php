<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigDescuento extends Model
{
    
    protected $fillable = [
        "type_descuento_id", "porcentaje", "minimo", "monto"
    ];


    public function typeDescuento()
    {
        return $this->belongsTo(TypeDescuento::class);
    }

}
