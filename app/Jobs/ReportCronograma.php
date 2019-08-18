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

/**
 * Genera Reportes
 */
class ReportCronograma implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $mes;
    private $year;
    private $adicional;
    public $timeout = 0;

    /**
     * @param string $mes
     * @param string $year
     * @param string $adicional
     */
    public function __construct($mes, $year, $adicional)
    {
        $this->mes = $mes;
        $this->year = $year;
        $this->adicional = $adicional;
    }

    /**
     * Genera un reporte en pdf del cronograma
     *
     * @return void
     */
    public function handle()
    {
        $metas = $metas = Meta::all();
        $pagina = 0;

        //traemos trabajadores que pertenescan a la meta actual
        foreach($metas as $meta) {

            $meta->mes = $this->mes;
            $meta->year = $this->year;

            //obtener a los trabajadores que esten en esta meta
            $works = Work::whereHas('infos', function($i) use($meta) {
                $i->where('infos.meta_id', $meta->id);
            })->with(['infos' => function($i) use($meta) {
                $i->where('infos.meta_id', $meta->id);
            }])->get();

            foreach($works as $work) {

                foreach ($work->infos as $info) {

                    //obtenemos las remuneraciones actuales del trabajador
                    $info->remuneraciones = Remuneracion::where("work_id", $work->id)
                            ->where("categoria_id", $info->categoria_id)
                            ->where("cargo_id", $info->cargo_id)
                            ->where("planilla_id", $info->planilla_id)
                            ->where("adicional", $this->adicional)
                            ->where("mes", $this->mes)
                            ->where("año", $this->year)
                            ->get();
                    
                    $total = $info->remuneraciones->sum('monto');

                    $tmp_base = $info->remuneraciones->where("base", 0)->sum('monto');
                    $tmp_base = $tmp_base == 0 ? $info->total : $tmp_base;

                    // agregamos datos a las remuneraciones
                    $info->remuneraciones->push([
                        "nombre" => "TOTAL",
                        "monto" => $total
                    ]);

                    //obtenemos los descuentos actuales del trabajador
                    $info->descuentos = Descuento::where("work_id", $work->id)
                            ->where('año', $meta->year)
                            ->where('mes', $meta->mes)
                            ->where('adicional', $this->adicional)
                            ->where('cargo_id', $info->cargo_id)
                            ->where('categoria_id', $info->categoria_id)
                            ->where('planilla_id', $info->planilla_id)
                            ->get();

                    $total_descto = $info->descuentos->where("base", 0)->sum('monto');

                    //calcular base imponible
                    $info->base = $tmp_base;

                    //calcular total de descuentos
                    $info->descuentos->push([
                        "nombre" => "TOTAL",
                        "monto" => $total_descto
                    ]);

                    //calcular essalud
                    $info->essalud = $info->descuentos->where("base", 1)->sum('monto');

                    //calcular total neto
                    $info->neto = $total - $total_descto;

                }

            }

            $meta->works = $works;

        }

        $meses = ["ENERO",'FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'];

        $pdf = PDF::loadView('pdf.planilla', compact('meses', 'metas', 'pagina'));
        $pdf->setPaper('a3', 'landscape')->setWarnings(false);

        $ruta = "/pdf/planilla_metas_{$this->mes}_{$this->year}_{$this->adicional}.pdf";
        $pdf->save(storage_path("app/public") . $ruta);

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification("/storage{$ruta}", "La planilla {$this->mes} del {$this->year} fué generada"));
        }

    }
}
