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
use App\Models\Info;
use App\Models\User;
use \Carbon\Carbon;
use App\Mail\SendBoleta;
use App\Models\Meta;
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
    private $meta_id;

    /**
     * @param string $mes
     * @param string $year
     * @param string $adicional
     */
    public function __construct($cronograma, $type_report, $meta_id)
    {
        $this->cronograma = $cronograma;
        $this->type_report = $type_report;
        $this->meta_id = $meta_id;
    }

    /**
     * Genera y procesa las boletas de los trabajadores
     *
     * @return void
     */
    public function handle()
    {
        $cronograma = $this->cronograma;
        $meta = Meta::findOrFail($this->meta_id);
        $infos = Info::with(["work" => function($w) {
            $w->orderBy("nombre_completo", 'ASC');
        }, "cargo", "categoria", "meta"])
        ->whereHas("meta", function($m) use($meta){
            $m->where("id", $meta->id);
        })->get();

        $collect = new CronogramaCollection($cronograma);
        $data = $collect->boleta($infos);

        $path = "pdf/boletas_meta_{$meta->metaID}_{$this->cronograma->mes}_{$this->cronograma->año}_{$this->cronograma->id}.pdf";
        $nombre = "Boletas del {$cronograma->mes} del {$cronograma->año} - Meta {$meta->metaID}";
        
        //genera el pdf;
        $pdf = PDF::loadView("pdf.boleta_auto", [
            'infos' => $data['infos'], 
            'cronograma' => $data['cronograma'], 
            'count' => $data['count']
        ]);

        $pdf->setPaper("a3", 'portrait')->setWarnings(false);
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
            $user->notify(new ReportNotification("/storage/{$path}", 
                $nombre
            ));
        }

    }
}
