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
use App\Models\Info;

/**
 * Genera Reportes
 */
class ReportCronograma implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $cronograma;
    private $type_report;
    private $meta_id;
    private $infoIn = [];
    public $timeout = 0;

    /**
     * @param string $mes
     * @param string $year
     * @param string $adicional
     */
    public function __construct($cronograma, $type_report, $infoIn, $meta_id)
    {
        $this->cronograma = $cronograma;
        $this->type_report = $type_report;
        $this->infoIn = $infoIn;
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
        $infos = Info::with("work")->whereIn("id", $this->infoIn)->get();
        $meta = Meta::findOrFail($this->meta_id);
        $pagina = 0;

        // configuracion
        $remuneraciones = Remuneracion::with("typeRemuneracion")->where("cronograma_id", $cronograma->id)->get();
        $all_retenciones = Descuento::with("typeDescuento")->where("cronograma_id", $cronograma->id)->get();
        $descuentos = $all_retenciones->where("base", 0);
        $aportaciones = $all_retenciones->where("base", 1);

        //traemos trabajadores que pertenescan a la meta actual
        $meta->mes = $cronograma->mes;
        $meta->year = $cronograma->año;

        //obtener a los trabajadores que esten en esta meta
        $tmp_infos = $infos->where("meta_id", $meta->id);

        foreach ($tmp_infos as $info) {

            // obtenemos las remuneraciones actuales del trabajador
            $tmp_remuneraciones = $remuneraciones->where("info_id", $info->id);
            // total de remuneraciones
            $total = $tmp_remuneraciones->sum('monto');
            // base imponible
            $tmp_base = $tmp_remuneraciones->where("base", 0)->sum('monto');
            // agregamos datos a las remuneraciones
            $tmp_remuneraciones->put(rand(1000, 9999), (Object)[
                "nivel" => 1,
                "key" => "TOTAL",
                "monto" => $total
            ]);
                    
            $info->remuneraciones = $tmp_remuneraciones->chunk(6);

            //obtenemos los descuentos actuales del trabajador
            $tmp_descuentos = $descuentos->where("info_id", $info->id);
            $total_descto = $tmp_descuentos->sum('monto');

            //calcular base imponible
            $info->base = $tmp_base;
            //calcular total neto
            $info->neto = $total - $total_descto;

            // agregamos el total neto
            $tmp_descuentos->put(rand(1000, 9999), (Object)[
                "nivel" => 1,
                "key" => "DESC",
                "monto" => $total_descto
            ]);

            $info->descuentos = $tmp_descuentos->chunk(6);

            $tmp_aportaciones = $aportaciones->where("info_id", $info->id);
            $total_aportaciones = $tmp_aportaciones->sum("monto");

            // total de las Aportaciones
            $tmp_aportaciones->put(rand(1000, 9999), (Object)[
                "nivel" => 1,
                "key" => "TOTAL",
                "monto" => $total_aportaciones
            ]);

            $info->aportaciones = $tmp_aportaciones;

        }

        $meta->infos = $tmp_infos->chunk(5);

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
            $archivo->update(["read" => 0]);
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
            $user->notify(new ReportNotification("/storage/pdf/{$nombre}", "{$archivo->name} fué generada"));
        }

    }
}
