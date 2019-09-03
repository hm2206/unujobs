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
use App\Models\TypeDetalle;

class ReportDescuentoTypeMulti implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $cronograma;
    private $type_report;
    private $type_descuentos;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($cronograma, $type_report, $type_descuentos)
    {
        $this->cronograma = $cronograma;
        $this->type_report = $type_report;
        $this->type_descuentos = $type_descuentos;
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
        $infos = $cronograma->infos;
        $count = 1;

        $types = TypeDescuento::whereIn("id", $this->type_descuentos)->get();

        
        // configuracion
        $descuentos = Descuento::whereIn("info_id", $infos->pluck(['id']))
            ->where("cronograma_id", $cronograma->id)
            ->whereIn("type_descuento_id", $this->type_descuentos)
            ->get();

        foreach ($infos as $info) {
      
            $info->descuentos = $descuentos->where("info_id", $info->id);
            $info->count = $count;
            $info->total = $info->descuentos->sum("monto");
            $count++;

        }

        // crear pdf
        $pdf = PDF::loadView("pdf.descuento_type", compact('cronograma', 'infos', 'meses', 'types'));
        $pdf->setPaper('a4', 'landscape')->setWarnings(false);

        $fecha = strtotime(Carbon::now());
        $name = "descuento_{$fecha}.pdf";
        $pdf->save(storage_path("app/public") . "/pdf/{$name}");

        $archivo = Report::create([
            "type" => "pdf",
            "name" => "Planilla del {$cronograma->mes} del {$cronograma->año}",
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
