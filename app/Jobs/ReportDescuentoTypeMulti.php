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
use App\Models\Historial;
use App\Notifications\ReportNotification;
use App\Models\TypeDescuento;
use App\Models\TypeRemuneracion;
use App\Models\TypeDetalle;
use App\Models\Detalle;

class ReportDescuentoTypeMulti implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $cronograma;
    private $type_report;
    private $type_descuento;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($cronograma, $type_report, $type_descuento)
    {
        $this->cronograma = $cronograma;
        $this->type_report = $type_report;
        $this->type_descuento = $type_descuento;
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
        $count = 1;
        
        $type = TypeDescuento::findOrFail($this->type_descuento);
        $type_detalles = TypeDetalle::where("type_descuento_id", $type->id)->get();
        
        // configuracion de los detalles
        $detalles =  Detalle::where("type_descuento_id", $type->id)
            ->where("cronograma_id", $cronograma->id)
            ->get();
        
        $infos = $cronograma->infos->whereIn("id", $detalles->pluck(['info_id']));
        // configurar infos
        $bodies = $infos->chunk(50);

        foreach ($bodies as $infos) {
            foreach ($infos as $info) {
                $info->type_detalles = $type_detalles;
                // configurar los descuentos  detalles x detalles
                foreach ($info->type_detalles as $type_detalle) {
                    $type_detalle->monto = $detalles->where("info_id", $info->id)
                        ->where("type_detalle_id", $type_detalle->id)
                        ->sum("monto");
                }

                $info->count = $count;
                $info->total = $detalles->where("info_id", $info->id)->sum("monto");
                $count++;
            }


            $tmp_detalle = [
                "nivel" => 1,
                "body" => []
            ];

            foreach($type_detalles as $total_type_detalle) {
                array_push($tmp_detalle["body"], (Object)[
                    "total" => $detalles->where("type_detalle_id", $total_type_detalle->id)
                        ->whereIn("info_id", $infos->pluck(['id']))
                        ->sum("monto")
                ]);
            }

            array_push($tmp_detalle["body"], (Object)[
                "total" => $infos->sum("total")
            ]);

            $infos->put(rand(10000, 99999), (Object)$tmp_detalle);

        }

        // crear pdf
        $pdf = PDF::loadView("pdf.descuento_detalle_type", compact('cronograma', 'bodies', 'meses', 'type', 'type_detalles' ,'detalles'));
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
