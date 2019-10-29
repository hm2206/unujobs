<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Exports\PlanillaExport;
use App\Models\TypeRemuneracion;
use App\Models\TypeCategoria;
use App\Models\TypeDescuento;
use App\Models\Cargo;
use App\Models\Cronograma;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Models\Afp;
use App\Models\Work;
use App\Notifications\ReportNotification;
use App\Models\TypeAfp;
use \PDF;
use App\Models\User;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;
use \Carbon\Carbon;
use App\Tools\Money;
use App\Models\Historial;
use App\Models\Aportacion;
use App\Models\TypeAportacion;

/**
 * Genera pdf de la planilla
 */
class GeneratePlanillaPDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $cronograma;
    public $timeout = 0;
    public $type_report;

    /**
     * configuramos un poco
     *
     * @param \App\Models\Cronograma $cronograma
     */
    public function __construct($cronograma, $type_report)
    {
        $this->cronograma = $cronograma;
        $this->type_report = $type_report;
    }

    /**
     * Generamos el pdf de las planilas generales
     *
     * @return void
     */
    public function handle()
    {
        $meses = [
            "Enero", "Febrero", "Marzo", "Abril", 
            "Mayo", 'Junio', 'Julio', 'Agosto', 
            'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];

        $cronograma = $this->cronograma;
        $historial = Historial::where('cronograma_id', $cronograma->id)->get();
        $money = new Money;

        $inTypeAFP = collect();
        $afps = Afp::orderBy("nombre", "ASC")->where('activo', 1)->get();
        // obtenemos los id de los descuentos
        foreach ($afps as $afp) {
            $inTypeAFP->push($afp->prima_descuento_id);
            $inTypeAFP->push($afp->aporte_descuento_id);
        }
        // obtenemos los type descuento de los afps
        $afpTypes = TypeAfp::whereHas("type_descuento")->get(); 
        // guardamos los type descuentos de los types afp
        foreach ($afpTypes as $type) {
            $inTypeAFP->push($type->type_descuento_id);
        }
        // obtenemos los tipos de descuentos y remuneraciones
        $type_remuneraciones = TypeRemuneracion::where("activo", 1)->where('report', 1)->get();
        $type_descuentos = TypeDescuento::where("activo", 1)
            ->whereNotIn("id", $inTypeAFP)
            ->where('base', 0)
            ->get();
        // optenere los tipos de aportaciones
        $type_aportaciones = TypeAportacion::where('activo', 1)->get();
        // obtener tipos de categorias que pertenecen a la planilla
        $type_categorias = TypeCategoria::with('cargos')
            ->whereHas('cargos', function($car) use($cronograma) {
                $car->where("planilla_id", $cronograma->planilla_id);
            })->get();

        // obtener las remuneraciones
        $remuneraciones = Remuneracion::where('cronograma_id', $cronograma->id)
            ->whereIn("type_remuneracion_id", $type_remuneraciones->pluck(['id']))
            ->get();
        // obtener los descuentos
        $descuentos = Descuento::where('cronograma_id', $cronograma->id)->get();
        // obtener las aportaciones 
        $aportaciones = Descuento::where('cronograma_id', $cronograma->id)
            ->whereIn("type_descuento_id", $type_aportaciones->pluck('type_descuento_id'))
            ->get();


        foreach ($type_remuneraciones as $type_remuneracion) {
            // obtenemos los tipos de categorias
            $type_remuneracion->type_categorias = $type_categorias;
            $type_remuneracion->total = $remuneraciones->where("type_remuneracion_id", $type_remuneracion->id)->sum("monto");
        }

        // configurar afps
        foreach($afps as $afp) {
            $typesAFP = $afp->type_afps->pluck(['type_descuento_id']);
            $pluck = [$afp->prima_descuento_id, $afp->aporte_descuento_id];
            $historialAFP = $historial->where('afp_id', $afp->id)->pluck('id');
            // almacenamos el monto de afp
            $afp_monto = $descuentos->whereIn("type_descuento_id", $pluck)
                ->whereIn('historial_id', $historialAFP)
                ->sum("monto");
            // almacenamos el monto del type afp
            $type_afp_monto = $descuentos->whereIn("type_descuento_id", $typesAFP)
                ->whereIn("historial_id", $historialAFP)
                ->sum('monto');
            // almacenamos el monto del afp
            $afp->monto = round($afp_monto + $type_afp_monto, 2);
        }

        // configuracion de los descuentos
        foreach ($type_descuentos as $desc) {
            $desc->monto = $descuentos->where("type_descuento_id", $desc->id)->sum('monto');
        }

        // configurar aportaciones
        $total_aportaciones = $descuentos->where('base', 1)->sum('monto');

        // configuramos las aportaciones
        foreach ($type_aportaciones as $aport) {
            $aport->monto = $descuentos->where("type_descuento_id", $aport->type_descuento_id)->sum("monto");
        }

        // configuracion de los totales
        $total_bruto = $historial->sum("total_bruto");
        // total de afps
        $afp_total = $afps->sum('monto');
        // total de los descuentos
        $total_descuentos = $historial->sum('total_desct');
        // obtenemos el total neto de la planilla
        $total_liquido = $historial->sum('total_neto');

        $sub_titulo = "RESUMEN GENERAL DE TODAS LAS METAS DE MES " . $meses[$cronograma->mes - 1] . " - " . $cronograma->año;
        $titulo = "";

        $pdf = PDF::loadView('pdf.cronograma', \compact(
            'type_remuneraciones', 'meses', 'cronograma', 
            'type_categorias', 'type_descuentos', "total_bruto",
            'sub_titulo','titulo', 'afps', 'total_descuentos',
            'type_aportaciones', 'total_aportaciones', 'remuneraciones',
            'total_liquido', 'money', 'afp_total'
        ));

        $pdf->setPaper('a3', 'landscape')->setWarnings(false);
        $path = "pdf/planilla_general_{$cronograma->mes}_{$cronograma->año}_{$cronograma->id}_v1.pdf";
        $nombre = "Resumen general del {$cronograma->mes} del {$cronograma->año} - v1";

        $pdf->save(storage_path("app/public") . "/{$path}");

        $archivo = Report::where("cronograma_id", $cronograma->id)
            ->where("type_report_id", $this->type_report)
            ->where("name", $nombre)
            ->first();

        if ($archivo) {
            $archivo->update([
                "read" => 0,
                "path" => "/storage/{$path}"
            ]);
        }else {
            $archivo = Report::create([
                "type" => "pdf",
                "name" => $nombre,
                "icono" => "fas fa-file-pdf",
                "path" => "/storage/{$path}",
                "cronograma_id" => $cronograma->id,
                "type_report_id" => $this->type_report
            ]);
        }

        $users = User::all();
        $message = $cronograma->planilla ? $cronograma->planilla->descripcion : '';

        foreach ($users as $user) {
            $user->notify(new ReportNotification($archivo->path ,"EL Reporte General v1 de la planilla {$message}, ya están listas"));
        }


    }

}
