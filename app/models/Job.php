<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    
    protected $fillable = [
        "ape_paterno", "ape_materno", "nombres","numero_de_documento",
        "fecha_de_nacimiento", "profesion", "phone","fecha_de_ingreso",
        "sexo", "numero_de_essalud", "banco_id", "numero_de_cuenta",
        "afp_id", "fecha_de_afiliacion", "numero_de_cussp", "accidentes",
        "categoria_id", "sindicato_id", "observaciones", "nombre_completo",
        "cargo_id", 'plaza', 'meta_id', 'pea', 'condicion_pap', 'perfil',
        'ext_pptto', 'escuela_id', 'ruc', 'fuente_id', "direccion"
    ];

    public function banco()
    {
        return $this->belongsTo(Banco::class);
    }

    public function sindicato()
    {
        return $this->belongsTo(Sindicato::class);
    }

    public function afp()
    {
        return $this->belongsTo(Afp::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }

    public function meta() 
    {
        return $this->belongsTo(Meta::Class);
    }

    public function remuneraciones()
    {
        return $this->hasMany(Remuneracion::class);
    }

}
