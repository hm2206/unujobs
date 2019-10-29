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
use App\Models\Historial;
use App\Notifications\ReportNotification;
use \PDF;
use App\Models\User;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;
use \Carbon\Carbon;
use App\Models\Meta;
use App\Tools\Money;
use App\Models\TypeAportacion;
use App\Models\TypeAfp;

/**
 * Genera pdf de la planilla
 */
class GeneratePlanillaMetaPDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $cronograma;
    public $timeout = 0;
    private $type_report;
    private $meta_id;

    /**
     * configuramos un poco
     *
     * @param \App\Models\Cronograma $cronograma
     */
    public function __construct($cronograma, $type_report, $meta_id)
    {
        $this->cronograma = $cronograma;
        $this->type_report = $type_report;
        $this->meta_id = $meta_id;
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

        $meta = Meta::findOrFail($this->meta_id);
        $cronograma = $this->cronograma;
        $historial = Historial::where("cronograma_id", $cronograma->id)->where('meta_id', $meta->id)->get();
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
        // obtenemos los tipos de descuentos, remuneraciones y aportaciones
        $type_remuneraciones = TypeRemuneracion::where("report", 1)->where("activo", 1)->get();
        $type_descuentos = TypeDescuento::where("activo", 1)
            ->whereNotIn("id", $inTypeAFP)
            ->where('base', 0)
            ->get();
        $type_aportaciones = TypeAportacion::where('activo', 1)->get();

        // obtener tipos de categorias que pertenecen a la planilla
        $type_categorias = TypeCategoria::with('cargos')
            ->whereHas('cargos', function($car) use($cronograma) {
                $car->where("planilla_id", $cronograma->planilla_id);
            })->get();
        // obtener las remuneraciones por meta presupuestal
        $remuneraciones = Remuneracion::whereIn("historial_id", $historial->pluck(['id']))
            ->whereIn("type_remuneracion_id", $type_remuneraciones->pluck(['id']))
            ->where('cronograma_id', $cronograma->id)
            ->get();
        // obtener descuentos por la meta presupuestal
        $descuentos = Descuento::whereIn("historial_id", $historial->pluck(['id']))
            ->where('cronograma_id', $cronograma->id)
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

        // total de los descuentos
        $total_descuentos = $historial->sum('total_desct');

        // configurar aportaciones
        $total_aportaciones = 0;

        // total de afps
        $afp_total = $afps->sum('monto');

        foreach ($type_aportaciones as $aport) {
            $aport->monto = $descuentos->where("type_descuento_id", $aport->type_descuento_id)->sum("monto");
            $total_aportaciones += $aport->monto;
        }

        // configuracion de los totales
        $total_bruto = $historial->sum('total_bruto');
        $total_liquido = $historial->sum('total_neto');

        $sub_titulo = "RESUMEN SIAF META {$meta->metaID} DEL MES " . $meses[$cronograma->mes - 1] . " - " . $cronograma->año;
        $titulo = $meta->metaID;

        $pdf = PDF::loadView('pdf.resumen_meta_por_meta', \compact(
            'type_remuneraciones', 'meses', 'cronograma', 
            'type_categorias', 'type_descuentos', "total_bruto",
            'sub_titulo','titulo', 'afps', 'total_descuentos',
            'type_aportaciones', 'total_aportaciones', 'remuneraciones',
            'total_liquido', 'afp_total', 'money'
        ));

        $pdf->setPaper('a3', 'landscape')->setWarnings(false);
        $path = "pdf/planilla_general_meta_{$meta->metaID}_{$cronograma->mes}_{$cronograma->año}_{$cronograma->id}_v1.pdf";
        $nombre = "Resumen general del {$cronograma->mes} del {$cronograma->año} - Meta {$meta->metaID} - v1";
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

        foreach ($users as $user) {
            $user->notify(new ReportNotification($cronograma->pdf ,"{$archivo->name}, ya está lista"));
        }


    }

}
