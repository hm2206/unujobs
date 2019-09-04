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
        $infos = $cronograma->infos;
        $count = 1;

        // configuracion
        $descuentos = Descuento::whereIn("info_id", $infos->pluck(['id']))
            ->where("cronograma_id", $cronograma->id)
            ->get();

        $remuneraciones = Remuneracion::whereIn("info_id", $infos->pluck(['id']))
            ->where("cronograma_id", $cronograma->id)
            ->get(); 

        // configurar
        $bodies = $infos->chunk(25);

        // reconfigurar los descuentos y remuneraciones
        $tmp_total = 0;
        foreach ($bodies as $infos) {
            foreach ($infos as $info) {
                $info->descuentos = $descuentos->where("info_id", $info->id)->where("base", 0);
                $info->aportaciones = $descuentos->where("info_id", $info->id)->where("base", 1);
                $info->total_descuentos = $descuentos->where("info_id", $info->id)->where("base", 0)->sum('monto');
                $info->total_bruto = $remuneraciones->where("info_id", $info->id)->sum('monto');
                $info->base_imponible = $remuneraciones->where("info_id", $info->id)->where("base", 0)->sum("monto");
                $info->total_neto = $info->total_bruto - $info->total_descuentos;
                $info->count = $count;
                $count++;
                $tmp_total += $info->total_neto;
            }

            $infos->put(rand(10000, 99999), (Object)[
                "nivel" => 1,
                "total" => $tmp_total
            ]);
        }

        // crear pdf
        $pdf = PDF::loadView("pdf.descuento", compact('cronograma', 'bodies', 'meses'));
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
