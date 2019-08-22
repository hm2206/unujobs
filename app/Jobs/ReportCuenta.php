<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Work;
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

        $cronograma = $this->cronograma;
        $bancos = Banco::all(); 
        
        foreach ($bancos as $banco) {

            $works = $cronograma->works->whereIn("banco_id", $banco->id)
                ->where("cheque", 0);

            $banco->count = $works->count();
            $banco->works = $works;

            foreach ($banco->works as $work) {
                
                $descuentos = Descuento::where("work_id", $work->id)
                    ->where("cronograma_id", $cronograma->id)
                    ->where("base", 0)
                    ->get();

                $remuneraciones = Remuneracion::where("work_id", $work->id)
                    ->where("cronograma_id", $cronograma->id)
                    ->get();

                $work->total_neto =  $remuneraciones->sum("monto") - $descuentos->sum('monto');

            }

        }

        
        $pdf = PDF::loadView("pdf.reporte_cuenta", compact('cronograma', 'bancos', 'meses'));

        $fecha = strtotime(Carbon::now());
        $name = "reporte_cuenta_{$fecha}.pdf";
        $pdf->save(storage_path("app/public") . "/pdf/{$name}");

        $archivo = Report::create([
            "type" => "pdf",
            "name" => "Reporte de Cuentas del {$cronograma->mes} del {$cronograma->año}",
            "icono" => "fas fa-file-pdf",
            "path" => "/storage/pdf/{$name}",
            "cronograma_id" => $this->cronograma->id,
            "type_report_id" => $this->type_report
        ]);

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification("/storage/pdf/{$name}", "EL Resumen de cuentas, {$cronograma->mes} del {$cronograma->year} fué generada"));
        }

    }
}
