<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Info extends Model
{
    
    protected $fillable = [
        "categoria_id", "cargo_id", "work_id", "active", "observacion"
    ];

    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

}
