<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\PlanillaExport;
use App\Models\TypeRemuneracion;
use App\Models\TypeCategoria;
use App\Models\TypeDescuento;
use App\Models\Cargo;
use App\Models\Cronograma;
use App\Models\Remuneracion;
use \PDF;
use App\Models\Meta;
use App\Jobs\GeneratePlanillaPDF;
use App\Jobs\GeneratePlanillaMetaPDF;

class ExportCronogramaController extends Controller
{
    
    public function pdf($id)
    {
        $cronograma = Cronograma::findOrFail($id);
        $cronograma->update(["pdf" => "", "pendiente" => 1]);
        GeneratePlanillaPDF::dispatch($cronograma);
        return back()->with(["success" => "Este proceso durará unos minutos... Vuelva más tarde"]);
    }


    public function planilla($id) 
    {
        $request = request();
        $metas = Meta::whereHas('works', function($q){})->with('works')->get();

        $config = [
            "mes" => $request->mes,
            "year" => $request->year
        ];

        GeneratePlanillaMetaPDF::dispatch($metas, $config);
        return "ready";
    }

}
