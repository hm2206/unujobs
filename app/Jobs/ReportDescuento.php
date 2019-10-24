<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\Descuento;
use App\Models\Remuneracion;
use \PDF;
use App\Models\Report;
use \Carbon\Carbon;
use App\Models\User;
use App\Notifications\ReportNotification;
use App\Models\TypeDescuento;
use App\Models\TypeRemuneracion;
use App\Models\Historial;

class ReportDescuento implements ShouldQueue
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
            "Enero", "Febrero", "Marzo", "Abril", "Mayo", 
            'Junio', 'Julio', 'Agosto', 'Septiembre', 
            'Octubre', 'Noviembre', 'Diciembre'
        ];

        $cronograma = $this->cronograma;
        $historial = Historial::where('cronograma_id', $cronograma->id)->get();
        $count = 1;

        // Obtener los descuentos
        $descuentos = Descuento::whereIn("historial_id", $historial->pluck(['id']))->get();
        // obtener las remuneraciones
        $remuneraciones = Remuneracion::where("show", 1)
            ->whereIn("historial_id", $historial->pluck(['id']))
            ->get(); 

        // configurar
        $bodies = $historial->chunk(25);

        // reconfigurar los descuentos y remuneraciones
        $tmp_total = 0;
        foreach ($bodies as $historial) {
            foreach ($historial as $history) {
                $history->descuentos = $descuentos->where("historial_id", $history->id)->where("base", 0);
                $history->aportaciones = $descuentos->where("historial_id", $history->id)->where("base", 1);
                $history->count = $count;
                $count++;
                $tmp_total = $history->total_neto;
            }
        }

        // crear pdf
        $pdf = PDF::loadView("pdf.descuento", compact('cronograma', 'bodies', 'meses', 'tmp_total'));
        $pdf->setPaper('a4', 'landscape')->setWarnings(false);

        $fecha = strtotime(Carbon::now());
        $name = "descuento_{$fecha}.pdf";
        $pdf->save(storage_path("app/public") . "/pdf/{$name}");

        $archivo = Report::create([
            "type" => "pdf",
            "name" => "Reporte de Todos los Descuentos del {$cronograma->mes} del {$cronograma->año}",
            "icono" => "fas fa-file-pdf",
            "path" => "/storage/pdf/{$name}",
            "cronograma_id" => $cronograma->id,
            "type_report_id" => $this->type_report
        ]);


        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification("/storage/pdf/{$name}", "El descuento del {$cronograma->mes} del {$cronograma->year} fué generada"));
        }

    }
}
