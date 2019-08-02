<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Info extends Model
{
    
    protected $fillable = [
        "categoria_id", "cargo_id", "work_id", "active", "observacion", 
        'meta_id', 'total', 'perfil', 'planilla_id', 'fuente_id', 'plaza',
        'escuela', 'observacion', 'ruc'
    ];

    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function meta()
    {
        return $this->belongsTo(Meta::class);
    }

    
    public function slug()
    {
        return \base64_encode($this->id);
    }
}
