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

class ReportCronograma implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $mes;
    private $year;
    private $adicional;

    public function __construct($mes, $year, $adicional)
    {
        $this->mes = $mes;
        $this->year = $year;
        $this->adicional = $adicional;
    }


    public function handle()
    {
        $metas = $metas = Meta::whereHas('works', function($q){})->with('works')->get();
        $pagina = 0;

        //traemos trabajadores que pertenescan a la meta actual
        foreach($metas as $meta) {

            $meta->mes = $this->mes;
            $meta->year = $this->year;

            foreach($meta->works as $work) {

                $total = $work->remuneraciones->where('año', $this->year)
                    ->where('mes', $this->mes)->where("adicional", $this->adicional)
                    ->sum("monto");

                $tmp_base = $work->remuneraciones->where("año", $this->year)
                    ->where('mes', $this->mes)->where("adicional", $this->adicional)
                    ->where("base", 0)->sum('monto');

                $tmp_base = $tmp_base == 0 ? $work->total : $tmp_base;

                $work->remuneraciones->push([
                    "nombre" => "TOTAL",
                    "monto" => $total
                ]);

                $work->descuentos = Descuento::where('año', $this->year)
                    ->where('mes', $this->mes)
                    ->where('adicional', $this->adicional)
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

            }

        }

        $meses = ["ENERO",'FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'];

        $pdf = PDF::loadView('pdf.planilla', compact('meses', 'metas', 'pagina'));
        $pdf->setPaper('a3', 'landscape')->setWarnings(false);

        $ruta = "/pdf/planilla_metas_{$this->mes}_{$this->year}.pdf";
        $pdf->save(public_path() . $ruta);

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification($ruta, "La planilla {$this->mes} del {$this->year} fué generada"));
        }

    }
}
