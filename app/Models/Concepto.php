<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Concepto extends Model
{
    
    protected $fillable = ["key", "descripcion"];


    public function slug()
    {
        return \base64_encode($this->id);
    }

}
