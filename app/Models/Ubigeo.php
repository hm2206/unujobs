<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ubigeo extends Model
{

    public function departamentos()
    {
        return $this->hasMany(Ubigeo::class, 'departamento_id');
    }
    
    public function provincias() 
    {
        return $this->hasMany(Ubigeo::class, 'provincia_id');
    }

}
