<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Historial;
use App\Models\Banco;
use \PDF;
use App\Models\User;
use App\Notifications\ReportNotification;
use App\Models\Report;
use \Carbon\Carbon;
use App\Models\Descuento;
use App\Models\Remuneracion;
use App\Models\TypeDescuento;
use App\Models\TypeRemuneracion;

class ReportCuenta implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $cronograma;
    private $type_report;
    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($cronograma, $type_report)
    {
        $this->cronograma = $cronograma;
        $this->type_report = $type_report;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $meses = $meses = [
            "Enero", "Febrero", "Marzo", "Abril", 
            "Mayo", 'Junio', 'Julio', 'Agosto', 
            'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];

        $num_work = 1;
        $num_page = 1;
        $totales = 0;
        $cronograma = $this->cronograma;
        $historial = Historial::with(['work'])
            ->where("cronograma_id", $cronograma->id)
            ->where("numero_de_cuenta", "<>", "")
            ->orWhere("numero_de_cuenta", "<>", null)
            ->get();

        // obtener tipo de bonificaciones
        $bonificaciones = TypeRemuneracion::where("bonificacion", 1)->get();
        // obtener remuneraciones
        $remuneraciones = Remuneracion::where("show", 1)
            ->whereIn("historial_id", $historial->pluck(['id']))
            ->whereIn("type_remuneracion_id", $bonificaciones->pluck(['id']))
            ->get();
        // obtener los bancos
        $bancos = Banco::whereIn('id', $historial->pluck(['banco_id']))->get();
        // configurar montos de los bancos
        foreach ($bancos as $banco) {
            // asignamos los trabajadores por banco asociado
            $banco->historial = $historial->where("banco_id", $banco->id);
            // configuramos a los trabajadores
            foreach ($banco->historial as  $history) {
                // obtenemos las remuneraciones por trabajador
                $history->remuneraciones = $remuneraciones->where("historial_id", $history->id);
            }
        }  
        
        $pdf = PDF::loadView("pdf.reporte_cuenta", compact('cronograma', 'bancos', 'meses', 'totales', 'num_work', 'num_page'));

        $fecha = strtotime(Carbon::now());
        $name = "reporte_cuenta_{$fecha}.pdf";
        $pdf->save(storage_path("app/public") . "/pdf/{$name}");

        $archivo = Report::where("cronograma_id", $this->cronograma->id)
            ->where("type_report_id", $this->type_report)
            ->first();
            
        if ($archivo) {
            $archivo->update([
                "path" => "/storage/pdf/{$name}",
                "read" => 0,
                "name" => "Reporte de Cuentas del {$cronograma->mes} del {$cronograma->año}"
            ]);
        }else {
            $archivo = Report::create([
                "type" => "pdf",
                "name" => "Reporte de Cuentas del {$cronograma->mes} del {$cronograma->año}",
                "icono" => "fas fa-file-pdf",
                "path" => "/storage/pdf/{$name}",
                "cronograma_id" => $this->cronograma->id,
                "type_report_id" => $this->type_report
            ]);    
        }

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification("/storage/pdf/{$name}", "EL Resumen de cuentas, {$cronograma->mes} del {$cronograma->year} fué generada"));
        }

    }
}
