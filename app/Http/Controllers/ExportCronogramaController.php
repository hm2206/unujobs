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
use App\Jobs\ReportCronograma;
use App\Jobs\ReportBoleta;

class ExportCronogramaController extends Controller
{
    /**
     * Crea un archivo de pdf del cronograma
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pdf($slug)
    {
        $id = \base64_decode($slug);
        $cronograma = Cronograma::findOrFail($id);
        $cronograma->update(["pdf" => "", "pendiente" => 1]);
        GeneratePlanillaPDF::dispatch($cronograma);
        return back()->with(["success" => "Este proceso durará unos minutos... Vuelva más tarde"]);
    }

    /**
     * Crea un archivos pdf del cronograma
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reporte($mes, $year, $condicion)
    {
        ReportCronograma::dispatch($mes, $year, $condicion);
        ReportBoleta::dispatch($mes, $year, $condicion);
        return back()->with(["success" => "Este proceso durará unos minutos... Vuelva más tarde"]);
    }

}
