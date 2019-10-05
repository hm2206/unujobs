<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boleta extends Model
{

    protected $table = 'info_cronograma';
    
    protected $fillable = [
        'cronograma_id', 'info_id', 'observacion', 'pap', 'ext_pptto',
        'afp_id', 'perfil', 'planilla_id', 'cargo_id', 'categoria_id',
        'numero_de_cussp', 'numero_de_essalud', 'meta_id', 'work_id',
        'numero_de_documento'
    ];


    // relaciones

    public function cronograma() {
        return $this->belongsTo(Cronograma::class);
    }


    public function info() {
        return $this->belongsTo(Info::class);
    }


    public function afp() {
        return $this->belongsTo(Afp::class);
    }


    public function planilla() {
        return $this->belongsTo(Planilla::class);
    }


    public function cargo() {
        return $this->belongsTo(Cargo::class);
    }


    public function categoria() {
        return $this->belongsTo(Categoria::class);
    }


    public function meta() {
        return $this->belongsTo(Meta::class);
    }

}
