<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    
    protected $fillable = [
        "name", "ruta", "modulo_id", "token", "icono"
    ];
    
    protected $hidden = ["token"];

    public function modulos() {
        return $this->hasMany(Modulo::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, "modulo_user");
    }

    public function parent()
    {
        return $this->belongsTo(Modulo::class, "modulo_id");
    }

}
