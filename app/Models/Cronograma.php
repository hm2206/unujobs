<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cronograma extends Model
{

    protected $fillable = [
        "mes", "año", "planilla_id", "adicional", "sede_id", "numero", "dias",
        "pdf", "pendiente", "observacion"
    ];
    
    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function planilla()
    {
        return $this->belongsTo(Planilla::class);
    }

    public function slug()
    {
        return \base64_encode($this->id);
    }

}
