<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormatoController extends Controller
{
    
    public function json()
    {
        return [
            "titulo" => "remuneraciones",
            "type" => "type_remuneracion_id",
            "sede_id" => "1",
            "planilla_id" => "",
            "mes" => "",
            "year" => "",
            "works" => [
                [
                    "numero_de_documento" => "71051564",
                    "montos" => [
                        "1" => "100"
                    ]
                ]
            ]
        ];
    }

}
