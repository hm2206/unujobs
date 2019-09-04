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
        $infoIn = $cronograma->infos->pluck(["id"]);
        $infos = Info::with("work")->whereIn("id", $infoIn)->get();

        $metas = $metas = Meta::whereIn("id", $infos->pluck(['meta_id']))->get();
        $pagina = 0;

        // configuracion
        $remuneraciones = Remuneracion::with("typeRemuneracion")->where("cronograma_id", $cronograma->id)->get();
        $descuentos = Descuento::with("typeDescuento")->where("cronograma_id", $cronograma->id)->where("base", 0)->get();

        //traemos trabajadores que pertenescan a la meta actual
        foreach($metas as $meta) {

            $meta->mes = $cronograma->mes;
            $meta->year = $cronograma->año;

            //obtener a los trabajadores que esten en esta meta
            $tmp_infos = $infos->where("meta_id", $meta->id);

            foreach ($tmp_infos as $info) {

                // obtenemos las remuneraciones actuales del trabajador
                $tmp_remuneraciones = $remuneraciones->where("info_id", $info->id);
                // total de remuneraciones
                $total = $tmp_remuneraciones->sum('monto');
                // base imponible
                $tmp_base = $tmp_remuneraciones->where("base", 0)->sum('monto');
                // agregamos datos a las remuneraciones
                $tmp_remuneraciones->put(rand(1000, 9999), (Object)[
                    "nivel" => 1,
                    "key" => "TOTAL",
                    "monto" => $total
                ]);
                    
                $info->remuneraciones = $tmp_remuneraciones->chunk(9);

                //obtenemos los descuentos actuales del trabajador
                $tmp_descuentos = $descuentos->where("info_id", $info->id);
                $total_descto = $tmp_descuentos->sum('monto');

                //calcular base imponible
                $info->base = $tmp_base;

                // agregamos datos a las remuneraciones
                $tmp_descuentos->put(rand(1000, 9999), (Object)[
                    "nivel" => 1,
                    "key" => "TOTAL",
                    "monto" => $total_descto
                ]);

                $info->descuentos = $tmp_descuentos->chunk(9);

                //calcular total neto
                $info->neto = $total - $total_descto;

            }

            
            $meta->infos = $tmp_infos->chunk(5);

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
