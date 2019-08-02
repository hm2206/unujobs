<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Afp extends Model
{
    
    protected $fillable = [
        "nombre", "flujo", "mixta", "aporte", "prima"
    ];

    public function slug()
    {
        return \base64_encode($this->id);
    }

}
