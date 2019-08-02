<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    
    protected $fillable = ["descripcion", "planilla_id", "tag"];

    public function slug()
    {
        return \base64_encode($this->id);
    }

    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'cargo_categoria');
    }

    public function typeCategoria()
    {
        return $this->belongsTo(TypeCategoria::class);
    }

    public function typeRemuneracions()
    {
        return $this->belongsToMany(TypeRemuneracion::class, 'cargo_type_remuneracion');
    }

    public function planilla()
    {
        return $this->belongsTo(Planilla::class);
    }

}
