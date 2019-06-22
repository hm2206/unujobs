<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Remuneracion extends Model
{
    
    public function concepto()
    {
        return $this->belongsTo(Concepto::class);
    }

}
