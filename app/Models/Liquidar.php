<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Liquidar extends Model
{
    
    protected $fillable = ["work_id", "fecha_de_cese", "monto", "mes", "año"];


    public function work()
    {
        return $this->belongsTo(Work::class);
    }

}
