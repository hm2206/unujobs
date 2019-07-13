<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    protected $fillable = [
        "numero_de_requerimiento", "sede_id", "dependencia_id", "oficina_id",
        "cargo_id", "cantidad", "honorarios", "meta_id", "fuente_id", "gasto",
        "periodo", "lugar_id", "perfil" , 'supervisora_id'
    ];
}
