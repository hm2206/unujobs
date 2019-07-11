<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    
    protected $fillable = [
        "id", "metaID", "meta", "sectorID", "sector",
        "pliegoID", "pliego", "unidadID", "unidad_ejecutora",
        "programa", "programaID", "funcionID", "funcion",
        "subProgramaID", "sub_programa", "actividadID",
        "actividad"
    ];

    public function works()
    {
        return $this->hasMany(Work::class);
    }

}
