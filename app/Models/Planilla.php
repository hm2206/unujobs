<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Planilla extends Model
{
    
    public function cargos()
    {
        return $this->hasMany(Cargo::class);
    }

}
