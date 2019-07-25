<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Planilla;
use App\Models\Cargo;
use App\Models\Meta;

class ApiRest extends Controller
{
    
    public function planilla()
    {
        return Planilla::all();
    }

    public function planillaShow($id)
    {
        return Planilla::with('cargos')->find($id);
    }

    public function cargoShow($id)
    {
        return Cargo::with("categorias")->find($id);
    }

    public function meta()
    {
        return Meta::all();
    }

}
