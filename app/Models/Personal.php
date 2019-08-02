<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    protected $fillable = [
        "numero_de_requerimiento", "sede_id", "dependencia_txt",
        "cargo_txt", "cantidad", "honorarios", "meta_id", "deberes",
        "fecha_inicio", "fecha_final", "lugar_txt", "bases", 
        'supervisora_txt', 'aceptado', 'convocatoria_id', 'slug'
    ];

    protected function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function meta()
    {
        return $this->belongsTo(Meta::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function postulantes()
    {
        return $this->belongsToMany(Postulante::class, 'etapas');
    }


    //cambiar o crear un nuevo slug
    public function changeSlug($replace, $id)
    {
        return \str_replace(" ", "-", $replace) . "-" . date('Y') . "-" . base64_encode($id);
    }

}
