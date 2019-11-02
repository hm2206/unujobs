<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Historial extends Model
{
    
    protected $fillable = [
        "work_id", "info_id", "planilla_id", "cargo_id",
        "categoria_id", "meta_id", "cronograma_id", "fuente_id",
        "sindicato_id", "afp_id", "type_afp_id", "numero_de_cussp", 
        "fecha_de_afiliacion", "fecha_de_ingreso", "fecha_de_cese",
        "banco_id", "numero_de_cuenta", "numero_de_essalud",
        "plaza", "perfil", "escuela", "pap", "ext_pptto", "ruc",
        "observacion", "base", "base_enc", "total_bruto", "total_neto",
        "total_desct", "pdf", "boleta", "afecto", "orden"
    ];


    public function work() 
    {
        return $this->belongsTo(Work::class);
    }

    public function info()
    {
        return $this->belongsTo(Info::class);
    }

    public function planilla()
    {
        return $this->belongsTo(Planilla::class);
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function meta()
    {
        return $this->belongsTo(Meta::class);
    }

    public function cronograma()
    {
        return $this->belongsTo(Cronograma::class);
    }

    public function sindicato()
    {
        return $this->belongsTo(Sindicato::class);
    }

    public function afp()
    {
        return $this->belongsTo(Afp::class);
    }

    public function type_afp()
    {
        return $this->belongsTo(TypeAfp::class);
    }

    public function remuneraciones()
    {
        return $this->hasMany(Remuneracion::class);
    }


    public function descuentos()
    {
        return $this->hasMany(Descuento::class);
    }


    public function aportaciones()
    {
        return $this->hasMany(Aportacion::class);
    }


    public function banco()
    {
        return $this->belongsTo(Banco::class);
    }

}
