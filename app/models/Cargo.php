<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    
    protected $fillable = ["descripcion", "planilla_id"];

    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'cargo_categoria');
    }

}
