<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\Work;
use App\Models\Cronograma;
use App\Models\TypeRemuneracion;
use App\Models\TypeDescuento;
use App\Models\Remuneracion;
use App\Models\Descuento;
use \PDF;
use \Mail;
use \DB;
use App\Models\User;
use \Carbon\Carbon;
use App\Mail\SendBoleta;
use App\Models\Report;
use App\Notifications\ReportNotification;
use App\Collections\CronogramaCollection;

/**
 * Generar Archivo PDF de las Boletas mesuales de un determinado cronograma
 */
class ReportBoleta implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  
    public $timeout = 0;
    private $cronograma;
    private $type_report;

    /**
     * @param string $mes
     * @param string $year
     * @param string $adicional
     */
    public function __construct($cronograma, $type_report)
    {
        $this->cronograma = $cronograma;
        $this->type_report = $type_report;
    }

    /**
     * Genera y procesa las boletas de los trabajadores
     *
     * @return void
     */
    public function handle()
    {
        $cronograma = $this->cronograma;
        // traer a los ids de los trabajadores de la planilla

        $collect = new CronogramaCollection($cronograma);
        $data = $collect->boleta();

        $fecha = strtotime(Carbon::now());
        $name = "boletas_{$this->cronograma->mes}_{$this->cronograma->año}_{$this->cronograma->adicional}_{$fecha}.pdf";
        
        //genera el pdf;
        $pdf = PDF::loadView("pdf.boleta_auto", [
            'infos' => $data['infos'], 
            'cronograma' => $data['cronograma'], 
            'count' => $data['count']
        ]);

        $pdf->setPaper('a4', 'landscape')->setWarnings(false);
        $pdf->save(storage_path("app/public/pdf/{$name}"));

        $archivo = Report::create([
            "type" => "pdf",
            "name" => "Boletas del {$cronograma->mes} del {$cronograma->año}",
            "icono" => "fas fa-file-pdf",
            "path" => "/storage/pdf/{$name}",
            "cronograma_id" => $this->cronograma->id,
            "type_report_id" => $this->type_report
        ]);

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification("/storage/pdf/{$name}", 
                "La boleta {$this->cronograma->mes} del {$this->cronograma->año} fué generada"
            ));
        }

    }
}
