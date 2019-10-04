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
        $workId = $cronograma->infos->pluck("work_id");
        $works = Work::whereIn("id", $workId)->get(["id", "afp_id"]);
        $money = new Money;

        $afps = Afp::orderBy("descripcion", "ASC")->where("activo", 1)->get();
        $type_remuneraciones = TypeRemuneracion::where("activo", 1)->get();
        $tmp_descuentos = TypeDescuento::where("activo", 1)->get();

        // VERIFICAR CAS
        if ($cronograma->planilla_id == 5) {
            $type_categorias = TypeCategoria::with('cargos')->where("id", 3)->get();
        }else {
            $type_categorias = TypeCategoria::with('cargos')->whereNotIn("id", [3])->get();
        }

        $remuneraciones = Remuneracion::where('cronograma_id', $cronograma->id)->get();
        $descuentos = Descuento::where('cronograma_id', $cronograma->id)->get();


        foreach ($type_remuneraciones as $type_remuneracion) {
            // obtenemos los tipos de categorias
            $type_remuneracion->type_categorias = $type_categorias;
            $type_remuneracion->total = $remuneraciones->where("type_remuneracion_id", $type_remuneracion->id)->sum("monto");
        }

        // configurar afps
        $whereIn = $tmp_descuentos->whereNotIn("config_afp", [null, "[]", ""])->pluck(["id"]);
        $afp_total = $descuentos->whereIn("type_descuento_id", $whereIn)->sum("monto");
        foreach($afps as $afp) {
            $afpIn = $works->where("afp_id", $afp->id)->pluck("id");
            $afp_monto = $descuentos->whereIn("work_id", $afpIn)
                ->whereIn("type_descuento_id", $whereIn)
                ->sum("monto");
                
            $afp->monto = $afp_monto;
        }

        // configuracion de los descuentos
        $type_descuentos = $tmp_descuentos->whereNotIn("id", $whereIn)->where("base", 0);
        foreach ($type_descuentos as $desc) {
            $desc->monto = $descuentos->where("type_descuento_id", $desc->id)->sum('monto');
        }


        // total de los descuentos
        $total_descuentos = $descuentos->where("base", 0)->sum("monto");

        // configurar aportaciones
        $type_aportaciones = $tmp_descuentos->where("base", 1);
        $total_aportaciones = 0;


        foreach ($type_aportaciones as $aport) {
            $aport->monto = $descuentos->where("type_descuento_id", $aport->id)->sum("monto");
            $total_aportaciones += $aport->monto;
        }

        // configuracion de los totales
        $total_bruto = $remuneraciones->sum("monto");
        $total_liquido = $total_bruto - $total_descuentos;

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
