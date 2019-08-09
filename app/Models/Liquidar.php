<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Liquidar extends Model
{
    
    protected $fillable = ["work_id", "fecha_de_cese", "monto"];

}
