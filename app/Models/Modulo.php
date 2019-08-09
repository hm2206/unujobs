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

    public function roles()
    {
        return $this->belongsToMany(Role::class, "role_modulo");
    }

    public function parent()
    {
        return $this->belongsTo(Modulo::class, "modulo_id");
    }

}
