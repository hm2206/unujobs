<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    
    protected $fillable = ["nombre", "monto"];

    public function cargos()
    {
        return $this->belongsToMany(Cargo::class);
    }

    public function conceptos()
    {
        return $this->belongsToMany(Concepto::class, 'categoria_concepto')->withPivot('monto');
    }


    public function slug()
    {
        return \base64_encode($this->id);
    }

}
