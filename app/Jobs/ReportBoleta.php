<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\Work;
use App\Models\Cronograma;
use App\Models\TypeRemuneracion;
use App\Models\TypeDescuento;
use App\Models\Remuneracion;
use App\Models\Descuento;
use \PDF;
use App\Models\User;
use App\Notifications\ReportNotification;


class ReportBoleta implements ShouldQueue
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

        $workIn = Remuneracion::where("mes", $this->mes)
            ->where("año", $this->year)
            ->where('adicional', $this->adicional)
            ->get()
            ->pluck(["work_id"]);

        $works = Work::whereIn("id", $workIn)->get();

        foreach ($works as $work) {
            
            foreach ($work->infos as $info) {

                $remuneraciones = Remuneracion::where("work_id", $work->id)
                    ->where("categoria_id", $info->categoria_id)
                    ->where("cargo_id", $info->cargo_id)
                    ->where("adicional", $this->adicional)
                    ->get();

                $descuentos = Descuento::with('typeDescuento')->where('work_id', $work->id)
                    ->where("categoria_id", $info->categoria_id)
                    ->where("cargo_id", $info->cargo_id)
                    ->where("adicional", $this->adicional)
                    ->get();
                
                $info->remuneraciones = $remuneraciones;
                $info->descuentos = $descuentos->chunk(2)->toArray();
                $info->total_descuento = $descuentos->sum('monto');


                //base imponible
                $info->base = $remuneraciones->where('base', 0)->sum('monto');

                //aportes
                $info->essalud = $info->base < 930 ? 83.7 : $info->base * 0.09;
                $info->accidentes = $work->accidentes ? ($info->base * 1.55) / 100 : 0;

                //total neto
                $info->neto = $info->total - $info->total_descuento;
                $info->total_aportes = $info->essalud + $info->accidentes;

            }

        }

        
        //genera el pdf;
        $pdf = PDF::loadView("pdf.boleta_auto", compact('works'));
        $pdf->setPaper('a4', 'landscape')->setWarnings(false);
        $pdf->save(storage_path("app/public/pdf/boletas_{$this->mes}_{$this->year}.pdf"));

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification("/storage/pdf/boletas_{$this->mes}_{$this->year}.pdf", 
                "La boleta {$this->mes} del {$this->year} fué generada"
            ));
        }

    }
}
