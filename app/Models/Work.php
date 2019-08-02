<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    
    protected $fillable = [
        "ape_paterno", "ape_materno", "nombres","numero_de_documento",
        "fecha_de_nacimiento", "profesion", "phone","fecha_de_ingreso",
        "sexo", "numero_de_essalud", "banco_id", "numero_de_cuenta",
        "afp_id", "type_afp", "fecha_de_afiliacion", "numero_de_cussp", "accidentes",
        "sindicato_id", "nombre_completo", "direccion", "total", "profesion", "email"
    ];

    public function banco()
    {
        return $this->belongsTo(Banco::class);
    }

    public function sindicato()
    {
        return $this->belongsTo(Sindicato::class);
    }

    public function sindicatos()
    {
        return $this->belongsToMany(Sindicato::class, 'sindicato_work');
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

    public function infos()
    {
        return $this->hasMany(Info::class);
    }

    public function meta() 
    {
        return $this->belongsTo(Meta::Class);
    }

    public function remuneraciones()
    {
        return $this->hasMany(Remuneracion::class);
    }


    public function slug()
    {
        return base64_encode($this->id);
    }

}
