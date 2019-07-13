<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use \PDF;
use App\Models\Work;
use App\Models\Descuento;

class GeneratePlanillaMetaPDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

 
    private $metas = [];
    private $config;

    public function __construct($metas, array $config)
    {
        $this->metas = $metas;
        $this->config = $config;
    }


    public function handle()
    {
        $metas = $this->metas;
        $pagina = 0;
        $config = $this->config;

        //traemos trabajadores que pertenescan a la meta actual
        $metas->map(function($meta) use($config) {

            $meta->mes = $config['mes'];
            $meta->year = $config['year'];

            $meta->works->map(function($work) use($config) {

                $total = $work->remuneraciones->where('año', $config['year'])->where('mes', $config['mes'])->sum("monto");
                $tmp_base = $work->remuneraciones->where("base", 0)->sum('monto');
                $tmp_base = $tmp_base == 0 ? $work->total : $tmp_base;

                $work->remuneraciones->push([
                    "nombre" => "TOTAL",
                    "monto" => $total
                ]);

                $work->descuentos = Descuento::where('año', $config['year'])
                    ->where('mes', $config['mes'])
                    ->where('work_id', $work->id)
                    ->get();

                $total_descto = $work->descuentos->sum('monto');

                //calcular base imponible
                $work->base = $tmp_base;

                //calcular total de descuentos
                $work->descuentos->push([
                    "nombre" => "TOTAL",
                    "monto" => $work->descuentos->sum('monto')
                ]);

                //calcular essalud
                $work->essalud = $work->base < 930 ? 83.7 : $work->base * 0.09;

                //calcular total neto
                $work->neto = $work->total - $total_descto;

                return $work;
            });

            return $meta;
        });

        $meses = ["ENERO",'FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'];

        $pdf = PDF::loadView('pdf.planilla', compact('meses', 'metas', 'pagina'));
        $pdf->setPaper('a3', 'landscape')->setWarnings(false);

        $ruta = "/pdf/planilla_metas.pdf";
        $pdf->save(public_path() . $ruta);
    }
}
