<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeCategoria extends Model
{

    public function cargos()
    {
        return $this->hasMany(Cargo::class);
    }

}
