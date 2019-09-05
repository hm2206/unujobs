<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Exports\PlanillaExport;
use App\Models\TypeRemuneracion;
use App\Models\TypeCategoria;
use App\Models\TypeDescuento;
use App\Models\Cargo;
use App\Models\Cronograma;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Models\Afp;
use App\Models\Work;
use App\Models\Info;
use App\Notifications\ReportNotification;
use \PDF;
use App\Models\User;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;
use \Carbon\Carbon;
use App\Models\Meta;

/**
 * Genera pdf de la planilla
 */
class ReportGeneralMeta implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $cronograma;
    public $timeout = 0;
    public $type_report;
    private $meta_id;

    /**
     * configuramos un poco
     *
     * @param \App\Models\Cronograma $cronograma
     */
    public function __construct($cronograma, $type_report, $meta_id)
    {
        $this->cronograma = $cronograma;
        $this->type_report = $type_report;
        $this->meta_id = $meta_id;
    }

    /**
     * Generamos el pdf de las planilas generales
     *
     * @return void
     */
    public function handle()
    {
        $meses = [
            "Enero", "Febrero", "Marzo", "Abril", 
            "Mayo", 'Junio', 'Julio', 'Agosto', 
            'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];

        $cronograma = $this->cronograma;
        $meta = Meta::findOrFail($this->meta_id);

        $type_remuneraciones = TypeRemuneracion::orderBy("id", "ASC")->where("activo", 1)->get();
        $type_descuentos = TypeDescuento::orderBy("id", "ASC")->where("activo", 1)->get();
        $only_descuentos = $type_descuentos->where("base", 0);
        $aportaciones = $type_descuentos->where("base", 1);
        $first_descuento = $type_descuentos->first();
        $first_remuneracion = $type_remuneraciones->first();

        // configuracion
        $remuneraciones = Remuneracion::where("meta_id", $this->meta_id)
            ->where("cronograma_id", $cronograma->id)
            ->get(["monto", "type_remuneracion_id", "meta_id"]);

        $descuentos = Descuento::where("meta_id", $this->meta_id)
            ->where("cronograma_id", $cronograma->id)
            ->get(["monto", "type_descuento_id", "meta_id"]);



        $espacios = 0;
            
        foreach($type_remuneraciones as $type_rem) {
            $type_rem->monto = $remuneraciones->where("type_remuneracion_id", $type_rem->id)->sum("monto");
        }
    
        foreach($only_descuentos as $type_desc) {
            $type_desc->monto = $descuentos->where("type_descuento_id", $type_desc->id)->sum("monto");
        }
    
        foreach($aportaciones as $aport) {
            $aport->monto = $descuentos->where("type_descuento_id", $aport->id)->sum("monto");
        }
    
        $total_remuneracion = $type_remuneraciones->sum('monto');
        $total_descuento = $only_descuentos->sum("monto");
        $total_aportacion = $aportaciones->sum("monto");
        $neto_remuneracion = $type_remuneraciones->sum("monto") - $total_descuento;
    
        // contar filas
        $num_remuneraciones = $type_remuneraciones->count();
        $num_aportaciones = $aportaciones->count();
        $num_descuentos = $only_descuentos->count() - $num_remuneraciones;
        $espacios = $num_remuneraciones - ($num_aportaciones + $num_descuentos + 2);
    
        $sub_titulo = "RESUMEN GENERAL DE LA META {$meta->id} DE MES " . $meses[$cronograma->mes - 1] . " - " . $cronograma->año;
        $titulo = "META " . $meta->metaID;
    
        $pdf = PDF::loadView('pdf.resumen_general_metas', \compact(
            'type_remuneraciones', 'sub_titulo', 'cronograma', 'meses',
            'type_descuentos', 'first_descuento', 'first_remuneracion',
            'only_descuentos', 'total_descuento', 'neto_remuneracion',
            'aportaciones', 'total_aportacion', 'espacios', 'titulo',
            "total_remuneracion"
        ));

        $pdf->setPaper('a3', 'landscape')->setWarnings(false);
        $path = "pdf/planilla_general_meta_{$meta->metaID}_{$cronograma->mes}_{$cronograma->año}_{$cronograma->id}_v2.pdf";
        $nombre = "Resumen general del {$cronograma->mes} del {$cronograma->año} - Meta {$meta->metaID} - v2";
        $pdf->save(storage_path("app/public") . "/{$path}");

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
            $user->notify(new ReportNotification($cronograma->pdf ,"La Planilla general se genero correctamente"));
        }

    }

}
