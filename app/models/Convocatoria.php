<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Convocatoria extends Model
{
    
    protected $fillable = [
        "numero_de_convocatoria", "observacion", "fecha_inicio", "fecha_final",
        "personal_id", "aceptado"
    ];

    public function personal()
    {
        return $this->belongsTo(Personal::class);
    }

}
