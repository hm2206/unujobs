<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\Meta;
use App\Models\Work;
use App\Models\Descuento;
use App\Models\Remuneracion;
use \PDF;
use App\Models\User;
use App\Notifications\ReportNotification;
use App\Models\Report;
use \Carbon\Carbon;
use App\Models\Historial;

/**
 * Genera Reportes
 */
class ReportCronograma implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $cronograma;
    private $type_report;
    private $meta_id;
    public $timeout = 0;

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
     * Genera un reporte en pdf del cronograma
     *
     * @return void
     */
    public function handle()
    {
        $cronograma = $this->cronograma;
        $meta = Meta::findOrFail($this->meta_id);
        $meta->mes = $cronograma->mes;
        $meta->year = $cronograma->año;
        $pagina = 1;

        // obtener historial
        $historial = Historial::where('cronograma_id', $cronograma->id)
            ->where('meta_id', $meta->id)
            ->get();

        // configuracion
        $remuneraciones = Remuneracion::with("typeRemuneracion")->where("show" , 1)
            ->whereIn("historial_id", $historial->pluck(['id']))
            ->get();
        // obtener descuentos
        $descuentos = Descuento::with("typeDescuento")->where("base", 0)
            ->whereIn("historial_id", $historial->pluck(['id']))
            ->get();
        // aportaciones
        $aportaciones = Descuento::with("typeDescuento")->where("base", 1)
            ->whereIn("historial_id", $historial->pluck(['id']))
            ->get();
        //traemos trabajadores que pertenescan a la meta actual
        foreach ($historial as $history) {
            // obtenemos las remuneraciones actuales del trabajador
            $tmp_remuneraciones = $remuneraciones->where("historial_id", $history->id);
            // total de remuneraciones
            $total = $history->total_bruto;
            // base imponible
            $tmp_base = $history->base;
            // agregamos datos a las remuneraciones
            $tmp_remuneraciones->put(rand(1000, 9999), (Object)[
                "nivel" => 1,
                "key" => "TOTAL",
                "monto" => $total
            ]);
                    
            $history->remuneraciones = $tmp_remuneraciones->chunk(6);

            //obtenemos los descuentos actuales del trabajador
            $tmp_descuentos = $descuentos->where("historial_id", $history->id);
            $total_descto = $history->total_desct;

            //calcular base imponible
            $history->base = $tmp_base;
            //calcular total neto
            $history->neto = $history->total_neto;

            // agregamos el total neto
            $tmp_descuentos->put(rand(1000, 9999), (Object)[
                "nivel" => 1,
                "key" => "DESC",
                "monto" => $total_descto
            ]);

            $history->descuentos = $tmp_descuentos->chunk(6);

            $tmp_aportaciones = $aportaciones->where("historial_id", $history->id);
            $total_aportaciones = $tmp_aportaciones->sum("monto");

            // total de las Aportaciones
            $tmp_aportaciones->put(rand(1000, 9999), (Object)[
                "nivel" => 1,
                "key" => "APORT",
                "monto" => $total_aportaciones
            ]);

            $history->aportaciones = $tmp_aportaciones;

        }

        $meta->historial = $historial->chunk(5);

        $meses = ["ENERO",'FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'];

        $pdf = PDF::loadView('pdf.planilla', compact('meses', 'meta', 'cronograma', 'pagina'));
        $pdf->setPaper('a3', 'landscape')->setWarnings(false);

        $path = "pdf/planilla_meta_{$meta->metaID}_{$cronograma->mes}_{$cronograma->año}_{$cronograma->id}.pdf";
        $nombre = "Planilla del {$cronograma->mes} del {$cronograma->año} - Meta {$meta->metaID}";
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
            $user->notify(new ReportNotification("/storage/{$path}", "{$archivo->name} fué generada"));
        }

    }
}
