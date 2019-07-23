<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeDescuento extends Model
{
    
    protected $fillable = ["descripcion", "key", "config_afp"];

    public function sindicatos()
    {
        return $this->belongsToMany(Sindicato::class, 'descuento_sindicato');
    }

    public function seguros()
    {
        return $this->belongsToMany(Afp::class, 'descuento_afp');
    }

}
