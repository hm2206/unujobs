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
use \PDF;
use App\Models\User;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;
use \Carbon\Carbon;
use App\Tools\Money;
use App\Models\TypeAportacion;
use App\Models\Historial;

/**
 * Genera pdf de la planilla
 */
class ReportGeneral implements ShouldQueue
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
        $money = new Money;
        $historial = Historial::where('cronograma_id', $cronograma->id);

        $type_remuneraciones = TypeRemuneracion::where("report", 1)->orderBy("orden", "ASC")->where("activo", 1)->get();
        $type_descuentos = TypeDescuento::orderBy("id", "ASC")->where('base', 0)->where("activo", 1)->get();
        $type_aportaciones = TypeAportacion::where('activo', 1)->get();
        // contadores
        $first_descuento = $type_descuentos->first();
        $first_remuneracion = $type_remuneraciones->first();

        // obtener remuneraciones
        $remuneraciones = Remuneracion::where("cronograma_id", $cronograma->id)
            ->whereIn("type_remuneracion_id", $type_remuneraciones->pluck(['id']))
            ->get(["monto", "type_remuneracion_id"]);
        // obtener descuentos
        $descuentos = Descuento::where("cronograma_id", $cronograma->id)->get(["monto", "type_descuento_id"]);
        $espacios = 0;

        foreach($type_remuneraciones as $type_rem) {
            $type_rem->monto = $remuneraciones->where("type_remuneracion_id", $type_rem->id)->sum("monto");
        }

        foreach($type_descuentos as $type_desc) {
            $type_desc->monto = $descuentos->where("type_descuento_id", $type_desc->id)->sum("monto");
        }

        foreach($type_aportaciones as $aport) {
            $aport->monto = $descuentos->where("type_descuento_id", $aport->id)->sum("monto");
        }

        $total_remuneracion = $historial->sum('total_bruto');
        $total_descuento = $historial->sum('total_desct');
        $total_aportacion = $type_aportaciones->sum("monto");
        $neto_remuneracion = $historial->sum('total_neto');

        // contar filas
        $num_remuneraciones = $type_remuneraciones->count();
        $num_aportaciones = $type_aportaciones->count();
        $num_descuentos = $type_descuentos->count() - $num_remuneraciones;
        $espacios = $num_remuneraciones - ($num_aportaciones + $num_descuentos + 2);

        $sub_titulo = "RESUMEN GENERAL DE TODAS LAS METAS DE MES " . $meses[$cronograma->mes - 1] . " - " . $cronograma->año;
        $titulo = "";

        $pdf = PDF::loadView('pdf.resumen_general_metas', \compact(
            'type_remuneraciones', 'sub_titulo', 'cronograma', 'meses',
            'type_descuentos', 'first_descuento', 'first_remuneracion',
            'type_aportaciones', 'total_descuento', 'neto_remuneracion',
            'total_aportacion', 'espacios', 'titulo',
            "total_remuneracion", 'money'
        ));

        $pdf->setPaper('a3', 'landscape')->setWarnings(false);
        $path = "pdf/planilla_general_{$cronograma->mes}_{$cronograma->año}_{$cronograma->id}_v2.pdf";
        $nombre = "Resumen general del {$cronograma->mes} del {$cronograma->año} - v2";

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
            $user->notify(new ReportNotification($archivo->path ,"EL Reporte General v2 de la planilla {$message}, ya están listas"));
        }

    }

}
