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
use App\Models\Report;
use \Carbon\Carbon;
use \DB;

class ReportBoletaWork implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $work;
    private $whereIn;
    public $timeout = 0;

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
        $cronogramas = Cronograma::whereIn("id", $this->whereIn)->get();
        $infos = $work->infos->whereIn("planilla_id", $cronogramas->pluck("planilla_id"));

        foreach ($infos as $info) {
            
            $info->cronogramas = $cronogramas;
            
            foreach ($info->cronogramas as $cro) {
                $tmp_remuneraciones = Remuneracion::where("work_id", $work->id)
                    ->where("planilla_id", $info->planilla_id)
                    ->where("cargo_id", $info->cargo_id)
                    ->where("categoria_id", $info->categoria_id)
                    ->where("cronograma_id", $cro->id)
                    ->get();
    
                $tmp_descuentos = Descuento::with('typeDescuento')
                    ->where('work_id', $work->id)
                    ->where("planilla_id", $info->planilla_id)
                    ->where("cargo_id", $info->cargo_id)
                    ->where("categoria_id", $info->categoria_id)
                    ->where("cronograma_id", $cro->id)
                    ->get();

                $cro->tmp_remuneraciones = $tmp_remuneraciones; 
                $cro->tmp_descuentos = $tmp_descuentos->where('base', 0);

                
                $cro->total_descuento = $cro->tmp_descuentos->where("base", 0)->sum("monto");
                $cro->total_remuneracion = $cro->tmp_remuneraciones->sum("monto");
    
    
                //base imponible
                $cro->base = $cro->tmp_remuneraciones->where('base', 0)->sum('monto');
                
                //aportes
                $cro->aportaciones = $tmp_descuentos->where("base", 1);
                
                //total neto
                $cro->neto = $cro->total_remuneracion - $cro->total_descuento;
                
            }
        }

        $pdf = PDF::loadView("pdf.boleta", compact('work', 'infos'));
        $pdf->setPaper('a4', 'landscape')->setWarnings(false);
        $work_name = strtoupper($work->numero_de_documento);
        $nombre = "boletas_" . date('Y-m-d') . "_{$work_name}.pdf";

        $pdf->save(storage_path("app/public/pdf/{$nombre}"));

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification("/storage/pdf/{$nombre}", 
                "La boletas de {$work->nombre_completo} fué generada"
            ));
        }

    }
}
