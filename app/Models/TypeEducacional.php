<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeEducacional extends Model
{
    
    protected $fillable = [
        'key', 'descripcion', 'type_descuento_id'
    ];


    public function type_descuento()
    {
        return $this->belongsTo(TypeDescuento::class);
    }
    

    public function educacionales()
    {
        return $this->hasMany(Educacional::class);
    }

}
