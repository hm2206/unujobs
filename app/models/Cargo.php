<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    
    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'cargo_categoria');
    }

}
