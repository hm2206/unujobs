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
use App\Jobs\GeneratePlanillaPDF;

class ExportCronogramaController extends Controller
{
    
    public function pdf($id)
    {
        $cronograma = Cronograma::findOrFail($id);
        $cronograma->update(["pdf" => "", "pendiente" => 1]);
        GeneratePlanillaPDF::dispatch($cronograma);
        return back()->with(["success" => "Este proceso durará unos minutos... Vuelva más tarde"]);
    }

}
