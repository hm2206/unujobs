<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Historial;
use \PDF;
use App\Models\User;
use App\Notifications\ReportNotification;
use App\Models\Report;
use \Carbon\Carbon;
use App\Models\Banco;
use App\Models\TypeRemuneracion;
use App\Models\Descuento;
use App\Models\Remuneracion;

class ReportCheque implements ShouldQueue
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

        $num_page = 1;
        $num_work = 1;
        $beforeTotal = 0;
        $beforeBon = [];

        $footer = \collect();

        $cronograma = $this->cronograma;
        $historial = Historial::with(['work:works.id,nombre_completo,numero_de_documento'])
            ->where("cronograma_id", $cronograma->id)
            ->where("numero_de_cuenta", '')
            ->orderBy('orden', 'ASC')
            ->get(["work_id", "id", "total_neto", "total_bruto"]);
        // obtener tipo de bonificaciones
        $bonificaciones = TypeRemuneracion::where("bonificacion", 1)->get();
        // obtener remuneraciones
        $remuneraciones = Remuneracion::where("show", 1)
            ->whereIn("historial_id", $historial->pluck(['id']))
            ->whereIn("type_remuneracion_id", $bonificaciones->pluck(['id']))
            ->get();
        
        $pdf = PDF::loadView("pdf.reporte_cheque", compact(
            'cronograma', 'bonificaciones', 'historial', 'meses', 'num_page', 'num_work',
            'beforeTotal', 'remuneraciones', 'beforeBon'
        ));

        $fecha = strtotime(Carbon::now());
        $name = "reporte_cheque_{$cronograma->id}.pdf";
        $pdf->save(storage_path("app/public") . "/pdf/{$name}");

        // obtenemos el reporte
        $archivo = Report::where("cronograma_id", $cronograma->id)
            ->where("type_report_id", $this->type_report)
            ->where("path", "/storage/pdf/{$name}")
            ->first();

        if ($archivo) {
            $archivo->update([
                "path" => "/storage/pdf/{$name}",
                "read" => 0,
                "name" => "Reporte de Cheques del {$cronograma->mes} del {$cronograma->año}"
            ]);
        }else {
            $archivo = Report::create([
                "type" => "pdf",
                "name" => "Reporte de Cheques del {$cronograma->mes} del {$cronograma->año}",
                "icono" => "fas fa-file-pdf",
                "path" => "/storage/pdf/{$name}",
                "cronograma_id" => $this->cronograma->id,
                "type_report_id" => $this->type_report
            ]);
        }

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification("/storage/pdf/{$name}", "EL Resumen de cheques, {$cronograma->mes} del {$cronograma->year} fué generada"));
        }

    }
}
