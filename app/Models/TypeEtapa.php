<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeEtapa extends Model
{
    
    protected $fillable = ['descripcion', 'icono'];


    public function postulantes()
    {
        return $this->belongsToMany(Postulante::class, 'etapas');
    }

}
