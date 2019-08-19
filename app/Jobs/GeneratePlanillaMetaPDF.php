<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use \PDF;
use App\Models\Work;
use App\Models\User;
use App\Notifications\ReportNotification;
use App\Models\Meta;
use App\Models\TypeRemuneracion;
use App\Models\TypeDescuento;
use App\Models\TypeCategoria;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Models\Afp;
use App\Models\Report;
use \Carbon\Carbon;

/**
 * Genera un pdf de las planillas meta por meta 
 */
class GeneratePlanillaMetaPDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Obtenemos el cronograma
     *
     * @var array
     */
    private $cronograma = [];
    public $timeout = 0;
    private $type_report;


    public function __construct($cronograma, $type_report)
    {
        $this->cronograma = $cronograma;
        $this->type_report = $type_report;
    }


    /**
     * Procesamos y creamos el PDF de la planilla
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
        $works = $cronograma->works;


        $metas = Meta::all();

        foreach ($metas as $meta) {
            
            $meta->type_remuneraciones = TypeRemuneracion::all();
            $meta->type_categorias = TypeCategoria::with('cargos')->get();
            $meta->type_descuentos = TypeDescuento::where("base", 0)->where("config_afp", null)->get();
            $meta->remuneraciones = Remuneracion::where('cronograma_id', $cronograma->id)
                ->where("meta_id", $meta->id)
                ->get();

            $meta->descuentos = Descuento::where('cronograma_id', $cronograma->id)
                ->where("meta_id", $meta->id)
                ->get();

            $meta->afps = Afp::all();

            $meta->results = collect();
            $meta->resumen = collect();
            $meta->totales = 0;
            $meta->rows = 0;

            // tipos de remuneración
            foreach ($meta->type_remuneraciones as $type) {

                $cats = collect();
                $total = 0;

                foreach($meta->type_categorias as $categoria) {

                    $tags = collect();

                    foreach ($categoria->cargos as $cargo) {
                        $tmp = $meta->remuneraciones->where('cargo_id', $cargo->id)
                            ->where('type_remuneracion_id', $type->id)
                            ->sum('monto');

                        $tags->push([
                            "id" => $cargo->id,
                            "descripcion" => $cargo->descripcion,
                            "monto" => $tmp
                        ]);

                        $total += $tmp;
                    }

                    $cats->push([
                        "id" => $categoria->id,
                        "descripcion" => $categoria->descripcion,
                        "tags" => $tags
                    ]);
                }

                $meta->results->push([
                    "id" => $type->id,
                    "descripcion" => $type->descripcion,
                    "categorias" => $cats,
                    "total" => $total
                ]);
            }


            // tipos de categorias
            foreach ($meta->type_categorias as $categoria) {
                $cars = collect();
                $suma = 0;
    
                foreach ($categoria->cargos as $cargo) {
                    $suma = $meta->remuneraciones->where('cargo_id', $cargo->id)->sum('monto');
                    $cars->push([
                        "id" => $cargo->descripcion,
                        "total" => $suma 
                    ]);
    
                    $meta->totales += $suma;
                }
    
                $meta->resumen->push([
                    "id" => $categoria['id'],
                    "descripcion" => $categoria['descripcion'],
                    "cargos" => $cars
                ]);
            }

            // tipos de descuentos
            foreach ($meta->type_descuentos as $desc) {
                $desc->monto = $meta->descuentos->where("type_descuento_id", $desc->id)
                    ->where("base", 0)
                    ->sum('monto');
            }
            
            // afps
            foreach($meta->afps as $afp) {
                $whereIn = TypeDescuento::where("base", 0)->where("config_afp", "<>", null)->get();
                $desct = Descuento::where("cronograma_id", $cronograma->id)
                    ->where("meta_id", $meta->id)
                    ->whereIn("type_descuento_id", $whereIn->pluck(['id']))
                    ->whereIn("work_id", $works->where("afp_id", $afp->id)->pluck(['id']))
                    ->get();
                $afp->monto = $desct->sum('monto');
            }

            // config
            $meta->total_descuentos = $meta->descuentos->where("base", 1)->sum('monto');
            

            $type_aportaciones = TypeDescuento::where("base", 1)->get();
            $aportaciones = collect();
            $total_aportaciones = 0;

            foreach ($type_aportaciones as $aport) {

                $tmp_aportes = Descuento::where("cronograma_id", $cronograma->id)
                    ->where("type_descuento_id", $aport->id)
                    ->get();

                if ($tmp_aportes->count() > 0) {

                    $monto = $tmp_aportes->sum('monto');

                    $aportaciones->push([
                        "key" => $aport->key,
                        "descripcion" => $aport->descripcion,
                        "monto" => $monto
                    ]);

                    $total_aportaciones += $monto;

                }

            }


            $meta->aportaciones = $aportaciones;
            $meta->total_aportaciones = $total_aportaciones;

        }


        $pdf = PDF::loadView('pdf.resumen_meta_por_meta', compact([
            "meses", "metas", "cronograma"
        ]));

        $fecha = \strtotime(Carbon::now());
        $nombre = "pdf/planilla_meta_x_meta_{$fecha}.pdf";
        $pdf->setPaper('a4', 'landscape')->setWarnings(false);
        $pdf->save(storage_path("app/public") . "/{$nombre}");

        $archivo = Report::create([
            "type" => "pdf",
            "name" => "Resumen general meta x meta del {$cronograma->mes} del {$cronograma->año}",
            "icono" => "fas fa-file-pdf",
            "path" => "/storage/{$nombre}",
            "cronograma_id" => $cronograma->id,
            "type_report_id" => $this->type_report
        ]);

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification($cronograma->pdf ,"La Planilla general meta x meta se genero correctamente"));
        }

    }
}
