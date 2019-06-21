<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    
    public function dependencias() 
    {
        return $this->belongsToMany(Dependencia::class, 'oficinas');
    }


    public function oficinas()
    {
        return $this->hasMany(Oficina::class);
    }

}
