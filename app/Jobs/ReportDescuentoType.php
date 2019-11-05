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
use App\Models\Historial;
use App\Tools\Money;

class ReportDescuentoType implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $cronograma;
    private $type_report;
    private $type_descuento;
    public $timeout = 0;

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
        $type = TypeDescuento::findOrFail($this->type_descuento);
        // historial
        $historial = Historial::whereHas('descuentos', function($des) use($type) {
                $des->where('descuentos.type_descuento_id', $type->id)
                    ->where('monto', '>', 0);
            })->where('cronograma_id', $cronograma->id)
            ->orderBy('orden', 'ASC')
            ->get();
            
        $count = 1;
        $money = new Money;

        

        // configuracion
        $descuentos = Descuento::whereIn("historial_id", $historial->pluck(['id']))
            ->where("type_descuento_id", $type->id)
            ->where('monto', '>', 0)
            ->get();
        // configurar
        $bodies = $historial->chunk(52);

        // reconfigurar los descuentos y remuneraciones
        $tmp_total = 0;
        foreach ($bodies as $historial) {
            foreach ($historial as $history) {
                $history->monto = $descuentos->where("historial_id", $history->id)->sum("monto");
                $history->count = $count;
                $count++;
            }
        }


        // crear pdf
        if ($type->base) {
            $pdf = PDF::loadView("pdf.aporte_detalle", compact('cronograma', 'bodies', 'meses', 'type', 'money'));
        }else {
            $pdf = PDF::loadView("pdf.descuento_type", compact('cronograma', 'bodies', 'meses', 'type', 'money'));
        }

        $message = strtolower($type->key);
        $name = "descuento_{$cronograma->mes}_{$cronograma->año}_{$message}.pdf";
        $pdf->save(storage_path("app/public") . "/pdf/{$name}");
        // obtener reporte
        $archivo = Report::where("path", "/storage/pdf/{$name}")
            ->where("type_report_id", $this->type_report)
            ->first();
        // verificar si hay el reporte
        if ($archivo) {
            $archivo->update([
                "name" => "El Descuento {$type->descripcion} del {$cronograma->mes} del {$cronograma->año}",
                "icono" => "fas fa-file-pdf",
                "path" => "/storage/pdf/{$name}",
                "read" => 0
            ]);
        }else {
            $archivo = Report::create([
                "type" => "pdf",
                "name" => "El Descuento {$type->descripcion} del {$cronograma->mes} del {$cronograma->año}",
                "icono" => "fas fa-file-pdf",
                "path" => "/storage/pdf/{$name}",
                "cronograma_id" => $cronograma->id,
                "type_report_id" => $this->type_report
            ]);
        }


        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification("/storage/pdf/{$name}", "{$archivo->name} fué generada"));
        }

    }
}
