<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\Meta;
use App\Models\Work;
use App\Models\Descuento;
use App\Models\Remuneracion;
use \PDF;
use App\Models\User;
use App\Notifications\ReportNotification;
use App\Models\Report;
use \Carbon\Carbon;
use App\Models\Info;

/**
 * Genera Reportes
 */
class ReportCronograma implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $cronograma;
    private $type_report;
    public $timeout = 0;

    /**
     * @param string $mes
     * @param string $year
     * @param string $adicional
     */
    public function __construct($cronograma, $type_report)
    {
        $this->cronograma = $cronograma;
        $this->type_report = $type_report;
    }

    /**
     * Genera un reporte en pdf del cronograma
     *
     * @return void
     */
    public function handle()
    {
        $cronograma = $this->cronograma;
        $infos = Info::where("planilla_id", $cronograma->planilla_id)->get();
        $metas = $metas = Meta::whereIn("id", $infos->pluck(['meta_id']))->get();
        $pagina = 0;

        //traemos trabajadores que pertenescan a la meta actual
        foreach($metas as $meta) {

            $meta->mes = $cronograma->mes;
            $meta->year = $cronograma->año;

            //obtener a los trabajadores que esten en esta meta
            $works = Work::whereHas('infos', function($i) use($meta) {
                $i->where('infos.meta_id', $meta->id);
            })->with(['infos' => function($i) use($meta) {
                $i->where('infos.meta_id', $meta->id);
            }])->get();

            foreach($works as $work) {

                $infos = $work->infos->where("planilla_id", $cronograma->planilla_id);

                foreach ($infos as $info) {

                    //obtenemos las remuneraciones actuales del trabajador
                    $tmp_remuneraciones = Remuneracion::where("work_id", $work->id)
                            ->where("categoria_id", $info->categoria_id)
                            ->where("cargo_id", $info->cargo_id)
                            ->where("planilla_id", $info->planilla_id)
                            ->where("cronograma_id", $cronograma->id)
                            ->get();

                    $info->remuneraciones = $tmp_remuneraciones;
                    $total = $tmp_remuneraciones->sum('monto');

                    $tmp_base = $info->remuneraciones->where("base", 0)->sum('monto');
                    $tmp_base = $tmp_base == 0 ? $total : $tmp_base;


                    // agregamos datos a las remuneraciones
                    $info->remuneraciones->push([
                        "nombre" => "TOTAL",
                        "monto" => $total
                    ]);

                    //obtenemos los descuentos actuales del trabajador
                    $tmp_descuentos= Descuento::where("work_id", $work->id)
                            ->where('cronograma_id', $cronograma->id)
                            ->where('cargo_id', $info->cargo_id)
                            ->where('categoria_id', $info->categoria_id)
                            ->where('planilla_id', $info->planilla_id)
                            ->get();

                    $info->descuentos = $tmp_descuentos->where("base", 0);
                    $total_descto = $info->descuentos->where("base", 0)->sum('monto');

                    //calcular base imponible
                    $info->base = $tmp_base;

                    //calcular total de descuentos
                    $info->descuentos->push([
                        "nombre" => "TOTAL",
                        "monto" => $total_descto
                    ]);

                    //calcular aportes
                    $info->aportaciones = $info->descuentos->where("base", 1);

                    //calcular total neto
                    $info->neto = $total - $total_descto;

                }

            }

            $meta->works = $works;

        }

        $meses = ["ENERO",'FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'];

        $pdf = PDF::loadView('pdf.planilla', compact('meses', 'metas', 'pagina'));
        $pdf->setPaper('a3', 'landscape')->setWarnings(false);

        $fecha = strtotime(Carbon::now());
        $name = "planilla_metas_{$fecha}.pdf";
        $pdf->save(storage_path("app/public") . "/pdf/{$name}");

        $archivo = Report::create([
            "type" => "pdf",
            "name" => "Planilla del {$cronograma->mes} del {$cronograma->año}",
            "icono" => "fas fa-file-pdf",
            "path" => "/storage/pdf/{$name}",
            "cronograma_id" => $this->cronograma->id,
            "type_report_id" => $this->type_report
        ]);

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification("/storage/pdf/{$name}", "La planilla del {$cronograma->mes} del {$cronograma->year} fué generada"));
        }

    }
}
