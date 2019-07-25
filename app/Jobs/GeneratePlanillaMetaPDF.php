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
use App\Models\User;
use App\Notifications\ReportNotification;

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

            //obtener a los trabajadores que esten en esta meta
            $works = Work::whereHas('infos', function($i) use($meta) {
                $i->where('infos.meta_id', $meta->id);
            })->with(['infos' => function($i) use($meta) {
                $i->where('infos.meta_id', $meta->id);
            }])->get();

            foreach ($works as $work) {
                
                foreach ($work->infos as $info) {

                    //obtenemos las remuneraciones actuales del trabajador
                    $info->remuneraciones = $work->remuneraciones->where('año', $config['year'])
                        ->where('mes', $config['mes'])
                        ->where('cargo_id', $info->cargo_id)
                        ->where('categoria_id', $info->categoria_id);
                    
                    $total = $info->remuneraciones->sum('monto');

                    $tmp_base = $info->remuneraciones->where("base", 0)->sum('monto');
                    $tmp_base = $tmp_base == 0 ? $info->total : $tmp_base;

                    // agregamos datos a las remuneraciones
                    $info->remuneraciones->push([
                        "nombre" => "TOTAL",
                        "monto" => $total
                    ]);

                    //obtenemos los descuentos actuales del trabajador
                    $info->descuentos = Descuento::where('año', $config['year'])
                            ->where('mes', $config['mes'])
                            ->where('cargo_id', $info->cargo_id)
                            ->where('categoria_id', $info->categoria_id)
                            ->get();

                    $total_descto = $info->descuentos->sum('monto');

                    //calcular base imponible
                    $info->base = $tmp_base;

                    //calcular total de descuentos
                    $info->descuentos->push([
                        "nombre" => "TOTAL",
                        "monto" => $info->descuentos->sum('monto')
                    ]);

                    //calcular essalud
                    $info->essalud = $info->base < 930 ? 83.7 : $info->base * 0.09;

                    //calcular total neto
                    $info->neto = $info->total - $total_descto;

                }

            }

            //retornamos las metas
            return $meta;

        });

        $meses = ["ENERO",'FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'];

        $pdf = PDF::loadView('pdf.planilla', compact('meses', 'metas', 'pagina'));
        $pdf->setPaper('a3', 'landscape')->setWarnings(false);

        $ruta = "/pdf/planilla_metas_{$config['mes']}_{$config['year']}.pdf";
        $pdf->save(storage_path('app/public') . $ruta);

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification("/storage{$ruta}" ,"La Planilla Meta x Meta se genero correctamente"));
        }
    }
}
