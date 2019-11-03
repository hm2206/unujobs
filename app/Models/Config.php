<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    
    protected $fillable = [
        "nombre", "alias", "logo", "email", "username",
        "copyright", "ruc", "direccion", "ceo", "cto"
    ];

}
