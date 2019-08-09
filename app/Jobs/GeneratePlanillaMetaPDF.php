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


    public function __construct($cronograma)
    {
        $this->cronograma = $cronograma;
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
            $meta->type_descuentos = TypeDescuento::where("config_afp", null)->get();
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
                $whereIn = TypeDescuento::where("config_afp", "<>", null)->get();
                $desct = Descuento::where("cronograma_id", $cronograma->id)
                    ->where("meta_id", $meta->id)
                    ->whereIn("type_descuento_id", $whereIn->pluck(['id']))
                    ->whereIn("work_id", $works->where("afp_id", $afp->id)->pluck(['id']))
                    ->get();
                $afp->monto = $desct->sum('monto');
            }

            // config
            $meta->total_descuentos = $meta->afps->sum('monto') + $meta->type_descuentos->sum('monto');
            $meta->total_essalud = 0;
            $meta->total_ies = 0;
            $meta->total_dlfp = 0;
            $meta->total_accidentes = 0;


            // trabajadores
            foreach ($works as $work) {
                $tmp_essalud = 0;
                $tmp_accidente = 0;
                $base = 0;
                $tmp_remuneraciones = Remuneracion::where("cronograma_id", $cronograma->id)
                    ->where("work_id", $work->id)
                    ->where("meta_id", $meta->id)
                    ->where('base', 0)
                    ->get();
    
                $base = $tmp_remuneraciones->sum('monto');
    
                //aportaciones current
                $tmp_essalud = $base < 930 ? 83.7 : $base * 0.09;
                $tmp_accidente = $work->accidentes ? ($base * 1.55) / 100 : 0;
    
                //totales
                $meta->total_essalud += $tmp_essalud;        
                $meta->total_accidentes += $tmp_accidente;
            }

            //calcular total de aportaciones
            $meta->total_aportaciones = $meta->total_accidentes + $meta->total_essalud + $meta->total_dlfp + $meta->total_ies;

        }


        $pdf = PDF::loadView('pdf.resumen_meta_por_meta', compact([
            "meses", "metas", "cronograma"
        ]));


        $pdf->setPaper('a4', 'landscape')->setWarnings(false);
        $nombre = "pdf/planilla_meta_x_meta_{$cronograma->id}_{$cronograma->mes}_{$cronograma->año}.pdf";

        $pdf->save(storage_path("app/public") . "/{$nombre}");
        $cronograma->update(["pdf_meta" => "/storage/{$nombre}", "pendiente" => 0]);

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification($cronograma->pdf ,"La Planilla general meta x meta se genero correctamente"));
        }

    }
}
