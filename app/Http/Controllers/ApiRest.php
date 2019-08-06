<?php
/**
 * ./app/Http/Controllers/ApiRest.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Planilla;
use App\Models\Cargo;
use App\Models\Meta;

/**
 * Class ApiRest
 * 
 * @category Controllers
 */
class ApiRest extends Controller
{
    /**
     * Muestra una lista de las recursos
     *
     * @return \App\Models\Planilla
     */
    public function planilla()
    {
        return Planilla::all();
    }

    /**
     * Muestra un recurso específico
     *
     * @param  \Illuminate\Http\Request $id
     * @return \App\Models\Planilla
     */
    public function planillaShow($id)
    {
        return Planilla::with('cargos')->find($id);
    }

    /**
     * Muestra un recurso específico
     *
     * @param  \Illuminate\Http\Request $id
     * @return \App\Models\Cargo
     */
    public function cargoShow($id)
    {
        return Cargo::with("categorias")->find($id);
    }


    /**
     * Muestra una lista de recursos
     *
     * @return \App\Models\Meta
     */
    public function meta()
    {
        return Meta::all();
    }

}
