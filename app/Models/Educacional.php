<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Educacional extends Model
{
    
    protected $fillable = [
        "type_educacional_id", "type_descuento_id", "work_id", 
        "info_id", "historial_id", "cronograma_id", "monto"
    ];


    public function type_educacional()
    {
        return $this->belongsTo(TypeEducacional::class);
    }


    public function type_descuento()
    {
        return $this->belongsTo(TypeDescuento::class);
    }


    public function work()
    {
        return $this->belongsTo(Work::class);
    }


    public function info()
    {
        return $this->belongsTo(Info::class);
    }


    public function historial()
    {
        return $this->belongsTo(Historial::class);
    }


    public function cronograma()
    {
        return $this->belongsTo(Crongorama::class);
    }

}
