<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use \PDF;
use App\Models\User;
use App\Models\Historial;
use App\Models\Work;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Models\Report;
use App\Notifications\ReportNotification;
use App\Tools\Money;
use App\Models\TypeDescuento;

class ReportPersonalCronograma implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $timeout = 0;

    private $cronograma;
    private $condicion;
    private $type_report;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($cronograma, $condicion, $type_report)
    {
        $this->cronograma = $cronograma;
        $this->condicion = $condicion;
        $this->type_report = $type_report;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $cronograma = $this->cronograma;
        $condicion = $this->condicion;
        $pages = [];
        $beforeBruto = 0;
        $beforeNeto = 0;
        $num_page = 1;
        $last_page = 1;
        $money = new Money;

        $meses = [
            "Enero", "Febrero", "Marzo", "Abril", 
            "Mayo", 'Junio', 'Julio', 'Agosto', 
            'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];

        $remuneraciones = Remuneracion::where("show", 1)->where("cronograma_id", $cronograma->id)->get();
        $typeDescuento = TypeDescuento::where("key", "43")->get();
        // obtener los descuentos necesarios
        $descuentos = Descuento::whereIn("type_descuento_id", $typeDescuento->pluck(['id']))
            ->where('cronograma_id', $cronograma->id)
            ->get();
        // obtener lista de los trabajadores
        $historial = Historial::with('work', 'categoria')
            ->where('cronograma_id', $cronograma->id)
            ->orderBy('orden', 'ASC')
            ->get();
        $pages = $condicion ? $historial->where('total_neto', '<=', 0)->chunk(48) : $historial->chunk(48);

        $message = $cronograma->planilla ? $cronograma->planilla->descripcion : '';
        $isCondicion = $condicion ? 'saldo Negativos' : 'todos los saldos';
        $path = "pdf/report_personal_{$cronograma->id}_{$condicion}.pdf";
        $nombre = "Reporte de Relación de {$isCondicion}";

        $pdf = PDF::loadView('pdf.reporte_personal', compact(
                'cronograma', 'pages', 'meses', 'money', 'beforeBruto', 'beforeNeto', 'num_page',
                'last_page'
        ));
        $pdf->setPaper("a4", 'portrait')->setWarnings(false);
        $pdf->save(storage_path("app/public/{$path}"));


        $archivo = Report::where("cronograma_id", $cronograma->id)
            ->where("type_report_id", $this->type_report)
            ->where("name", $nombre)
            ->first();

        if ($archivo) {
            $archivo->update([
                "read" => 0,
                "path" => "/storage/{$path}"
            ]);
        }else {
            $archivo = Report::create([
                "type" => "pdf",
                "name" => $nombre,
                "icono" => "fas fa-file-pdf",
                "path" => "/storage/{$path}",
                "cronograma_id" => $cronograma->id,
                "type_report_id" => $this->type_report
            ]);
        }

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification($archivo->path, "Reporte de Relación de Personal de la planilla {$message}"));
        }

    }
}
