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

        $type_remuneraciones = TypeRemuneracion::all();
        $type_categorias = TypeCategoria::with('cargos')->get();
        $type_descuentos = TypeDescuento::where("config_afp", null)->where("base", 0)->get();
        $remuneraciones = Remuneracion::where('cronograma_id', $cronograma->id)->get();
        $descuentos = Descuento::where('cronograma_id', $cronograma->id)->get();

        $afps = Afp::all();
        $works = Work::whereIn("id", $remuneraciones->pluck(['work_id']))->get();

        $results = collect();
        $resumen = collect();
        $totales = 0;
        $rows = 0;

        foreach ($type_remuneraciones as $type) {

            $cats = collect();
            $total = 0;

            foreach($type_categorias as $categoria) {

                $tags = collect();

                foreach ($categoria->cargos as $cargo) {
                    $tmp = $remuneraciones->where('cargo_id', $cargo->id)
                        ->where('type_remuneracion_id', $type->id)
                        ->sum('monto');

                    $tags->push([
                        "id" => $cargo->id,
                        "descripcion" => $cargo->descripcion,
                        "monto" => $tmp
                    ]);

                    $total += $tmp;
                }

                $cats->push([
                    "id" => $categoria->id,
                    "descripcion" => $categoria->descripcion,
                    "tags" => $tags
                ]);
            }

            $results->push([
                "id" => $type->id,
                "descripcion" => $type->descripcion,
                "categorias" => $cats,
                "total" => $total
            ]);
        }

        foreach ($type_categorias as $categoria) {
            $cars = collect();
            $suma = 0;

            foreach ($categoria->cargos as $cargo) {
                $suma = $remuneraciones->where('cargo_id', $cargo->id)->sum('monto');
                $cars->push([
                    "id" => $cargo->descripcion,
                    "total" => $suma 
                ]);

                $totales += $suma;
            }

            $resumen->push([
                "id" => $categoria['id'],
                "descripcion" => $categoria['descripcion'],
                "cargos" => $cars
            ]);
        }

        foreach ($type_descuentos as $desc) {
            $desc->monto = $descuentos->where("type_descuento_id", $desc->id)->sum('monto');
        }

        foreach($afps as $afp) {
            $whereIn = TypeDescuento::where("config_afp", "<>", null)->get();
            $desct = Descuento::where("cronograma_id", $cronograma->id)
                ->whereIn("type_descuento_id", $whereIn->pluck(['id']))
                ->whereIn("work_id", $works->where("afp_id", $afp->id)->pluck(['id']))
                ->get();

            $afp->monto = $desct->sum('monto');
        }


        $total_descuentos = $descuentos->where("base", 0)->sum("monto");

        $type_aportaciones = TypeDescuento::where("base", 1)->get();
        $aportaciones = collect();
        $total_aportaciones = 0;

        foreach ($type_aportaciones as $aport) {

            $tmp_aportes = Descuento::where("cronograma_id", $cronograma->id)
                ->where("type_descuento_id", $aport->id)
                ->get();

            if ($tmp_aportes->count() > 0) {

                $monto = $tmp_aportes->sum('monto');

                $aportaciones->push([
                    "key" => $aport->key,
                    "descripcion" => $aport->descripcion,
                    "monto" => $monto
                ]);

                $total_aportaciones += $monto;

            }

        }
        

        $sub_titulo = "RESUMEN GENERAL DE TODAS LAS METAS DE MES " . $meses[$cronograma->mes - 1] . " - " . $cronograma->año;
        $titulo = "";


        $pdf = PDF::loadView('pdf.cronograma', \compact(
            'type_remuneraciones', 'results', 'resumen', 
            'meses', 'cronograma', 'type_categorias',
            'totales', 'type_descuentos', 'rows', 
            'sub_titulo','titulo', 'afps', 'total_descuentos',
            'aportaciones', 'total_aportaciones'
        ));

        $fecha = \strtotime(Carbon::now());
        $pdf->setPaper('a4', 'landscape')->setWarnings(false);
        $nombre = "pdf/planilla_general_{$fecha}.pdf";

        $pdf->save(storage_path("app/public") . "/{$nombre}");

        $archivo = Report::create([
            "type" => "pdf",
            "name" => "Resumen general del {$cronograma->mes} del {$cronograma->año}",
            "icono" => "fas fa-file-pdf",
            "path" => "/storage/{$nombre}",
            "cronograma_id" => $cronograma->id,
            "type_report_id" => $this->type_report
        ]);

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification($cronograma->pdf ,"La Planilla general se genero correctamente"));
        }


    }

}
