<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Notifications\ReportNotification;
use \PDF;
use App\Models\Work;
use App\Models\Cronograma;
use App\Models\TypeRemuneracion;
use App\Models\TypeDescuento;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Models\User;

class ReportBoletaWork implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $work;
    private $whereIn;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($work, $whereIn)
    {
        $this->work = $work;
        $this->whereIn = $whereIn;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $work = $this->work;
        $infos = $work->infos;

        foreach ($infos as $info) {
            
            $info->cronogramas = Cronograma::whereIn("id", $this->whereIn)->get();

            foreach ($info->cronogramas as $cro) {
                $cro->tmp_remuneraciones = Remuneracion::where("work_id", $work->id)
                    ->where("planilla_id", $info->planilla_id)
                    ->where("cargo_id", $info->cargo_id)
                    ->where("categoria_id", $info->categoria_id)
                    ->where("cronograma_id", $cro->id)
                    ->get();
    
                $cro->tmp_descuentos = Descuento::with('typeDescuento')->where('work_id', $work->id)
                    ->where("planilla_id", $info->planilla_id)
                    ->where("cargo_id", $info->cargo_id)
                    ->where("categoria_id", $info->categoria_id)
                    ->where("cronograma_id", $cro->id)
                    ->get();
                
                $cro->total_descuento = $cro->tmp_descuentos->sum("monto");
                $cro->total_remuneracion = $cro->tmp_remuneraciones->sum("monto");
    
    
                //base imponible
                $cro->base = $cro->tmp_remuneraciones->where('base', 0)->sum('monto');
                
                //aportes
                $cro->essalud = $cro->base < 930 ? 83.7 : $cro->base * 0.09;
                $cro->accidentes = $work->accidentes ? ($base * 1.55) / 100 : 0;
                
                //total neto
                $cro->neto = $cro->total_remuneracion - $cro->total_descuento;
                $cro->total_aportes = $cro->essalud + $cro->accidentes;
                
            }
        }

        $pdf = PDF::loadView("pdf.boleta", compact('work', 'infos'));
        $pdf->setPaper('a4', 'landscape')->setWarnings(false);

        $nombre = "pdf/boletas_" . date('Y-m-d') . "_{$work->numero_de_documento}.pdf";

        $pdf->save(storage_path("app/public/{$nombre}"));

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification("/storage/{$nombre}", 
                "La boletas de {$work->nombre_completo} fué generada"
            ));
        }

    }
}
