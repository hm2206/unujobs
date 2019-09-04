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

class ReportDescuentoType implements ShouldQueue
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
        $infos = $cronograma->infos;
        $count = 1;

        $type = TypeDescuento::findOrFail($this->type_descuento);

        // configuracion
        $descuentos = Descuento::whereIn("info_id", $infos->pluck(['id']))
            ->where("cronograma_id", $cronograma->id)
            ->where("type_descuento_id", $this->type_descuento)
            ->get();

        // configurar
        $bodies = $infos->chunk(50);

        // reconfigurar los descuentos y remuneraciones
        $tmp_total = 0;

        foreach ($bodies as $infos) {
            foreach ($infos as $info) {
                $info->tmp_monto = $descuentos->where("info_id", $info->id)->sum("monto");
                $info->count = $count;
                $count++;
                $tmp_total += $info->tmp_monto;
            }

            $infos->put(rand(10000, 99999), (Object)[
                "nivel" => 1,
                "total" => $tmp_total
            ]);
        }


        // crear pdf
        $pdf = PDF::loadView("pdf.descuento_type", compact('cronograma', 'bodies', 'meses', 'type'));
        $fecha = strtotime(Carbon::now());
        $name = "descuento_{$fecha}.pdf";
        $pdf->save(storage_path("app/public") . "/pdf/{$name}");

        $archivo = Report::create([
            "type" => "pdf",
            "name" => "El Descuento {$type->descripcion} del {$cronograma->mes} del {$cronograma->año}",
            "icono" => "fas fa-file-pdf",
            "path" => "/storage/pdf/{$name}",
            "cronograma_id" => $cronograma->id,
            "type_report_id" => $this->type_report
        ]);


        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification("/storage/pdf/{$name}", "{$archivo->name} fué generada"));
        }

    }
}
