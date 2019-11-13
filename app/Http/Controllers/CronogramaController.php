<?php
/**
 * Http/Controllers/CronogramaController.php
 * 
 * @author Hans <twd2206@gmail.com>
 */
namespace App\Http\Controllers;

use App\Models\Cronograma;
use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Remuneracion;
use App\Models\Work;
use App\Models\Planilla;
use App\Models\Meta;
use App\Models\TypeRemuneracion;
use App\Models\TypeDescuento;
use App\Models\Descuento;
use App\Jobs\ProssingRemuneracion;
use App\Jobs\ProssingDescuento;
use App\Jobs\ReportBoleta;
use App\Jobs\ReportCronograma;
use Illuminate\Support\Facades\Storage;
use \DB;
use App\Models\Cargo;
use App\Models\Info;
use App\Models\TypeReport;
use App\Http\Middleware\CronogramaMiddleware;
use App\Jobs\DetachInfoJob;
use App\Jobs\AddInfoCronograma;
use App\Jobs\ProssingAportacion;
use App\Models\Historial;
use App\Models\Educacional;
use App\Models\Aportacion;
use App\Jobs\TurnOffPlanilla;
use App\Jobs\SendMailBoletas;
use App\Collections\DescuentoCollection;

/**
 * Class CronogramaController
 * 
 * @category Controllers
 */
class CronogramaController extends Controller
{

    public function __construct()
    {
        $this->middleware("auditoria:crear planilla")->only(['store']);
        $this->middleware("import")->only('estado');
    }

    /**
     * Muestra una lista cronogramas de planillas
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $mes = request()->mes ? (int)request()->mes : (int)date('m');
        $year = request()->year ? (int)request()->year : (int)date('Y');
        $mes = $mes == 0 || $mes > 12 ? (int)date('m') : $mes;
        $year = $year > date('Y') ? date('Y') : $year;

        $categoria_id = "";

        $cronogramas = Cronograma::where('mes', $mes)
            ->where('año', $year)
            ->paginate(20);

        $reporte = Storage::disk('public')->exists("pdf/report_personal_general_{$year}_{$mes}.pdf")
            ? "/storage/pdf/report_personal_general_{$year}_{$mes}.pdf"
            : null;

        return view('cronogramas.index', 
            \compact(
                'cronogramas', 'categoria_id', 'mes', 'year', 'reporte'
            ));
    }

    /**
     * Redirecciona a la ruta de origen
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        return back();
    }

    /**
     * Almacena un cronograma recien creada
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate(request(), [
            "planilla_id" => "required",
            "mes" => "required",
            "dias" => "required",
        ]);

        $mes = $request->mes <= 12 && $request->mes > 0 ? (int)$request->mes : (int)date('m');
        $pendiente = 1;
        $current_mes = (int)date('m') + 1;


        if ($mes > $current_mes || $mes < $current_mes - 1) {
            return [
                "status" => false,
                "message" => "El mes no está permitido!"
            ];
        }

        $year = date('Y');
        $adicional = $request->adicional ? 1 : 0;


        $cronogramas = Cronograma::where('año', $year)
            ->where('planilla_id', $request->planilla_id)
            ->where('adicional', $adicional)
            ->where('mes', $mes)->get();

        if ($adicional == 0 && $cronogramas->count() > 0) {
            return [
                "status" => false,
                "message" => "El cronograma ya existe"
            ];
        }elseif($adicional == 1 && $cronogramas->count() < 1) {
            return [
                "status" => false,
                "message" => "El cronograma principal aun no está creado"
            ];
        }

        $cronograma = Cronograma::create($request->except('adicional'));

        
        $cronograma->update([
            "mes" => $mes,
            "año" => $year,
            "adicional" => $adicional,
            "pendiente" => 1
        ]);
            
        // actualizamos el pendiente a listo en las planillas adicionales
        if ($cronograma->adicional == 1) {
            $cronograma->update(["pendiente" => 0]);
        }

        if($cronograma->adicional == 0) {
            $infoIn = [];

            AddInfoCronograma::withChain([
                (new ProssingRemuneracion($cronograma))->onQueue('high'),
                (new ProssingDescuento($cronograma))->onQueue('high'),
                (new ProssingAportacion($cronograma))->onQueue('high'),
            ])->dispatch($cronograma)
                ->onQueue('medium');

        }elseif($cronograma->adicional == 1) {
            $cronograma->update([
                "numero" => $cronogramas->count(),
                "estado" => 1,
            ]);
        }

        return [
            "status" => true,
            "message" => "Los datos se guardarón correctamente"
        ];
    }


    /**
     * Undocumented function
     *
     * @param Cronograma $cronograma
     * @return void
     */
    public function show($id)
    {
        $cronograma = Cronograma::with(["works", "planilla"])->findOrFail($id);
        return $cronograma;
    }


    /**
     * Muestra un formulario para editar un cronograma
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function historial($id)
    {   
        $like = request()->query_search;
        $cronograma = Cronograma::findOrFail($id);
        return Historial::with('work', 'categoria')
            ->where("cronograma_id", $cronograma->id)
            ->whereHas("work", function($i) use($like) {
                $i->where("nombre_completo", "like", "%{$like}%")
                    ->orWhere("numero_de_documento", "like", "%{$like}%");
            })->orderBy('orden', 'DESC')
                ->paginate(30);
    }

    
    /**
     * Actualiza un cronograma recien modificado
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cronograma $cronograma
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->validate(request(), [
            "planilla_id" => "required",
            "observacion" => "max:225"
        ]);

        $cronograma = Cronograma::where("estado", 1)->findOrFail($id);
        $cronograma->update(["observacion" => $request->observacion]);
        return [
            "status" => true,
            "message" => "Los datos se actualizarón correctamente"
        ];
    }


    /**
     * Undocumented function
     *
     * @param Cronograma $cronograma
     * @return void
     */
    public function destroy(Cronograma $cronograma)
    {
        return back();
    }

    /**
     * Muestra al cronograma con sus respectivos trabajadores
     *
     * @param  string $slug
     * @return \Illuminate\View\View
     */
    public function job($slug)
    {
        $id = \base64_decode($slug);
        $cronograma = Cronograma::with("planilla")->where("pendiente", 0)->findOrFail($id);
        $typeReports = TypeReport::all();
        $like = request()->query_search;
        $meta_id = request()->meta_id;
        $cargo_id = request()->cargo_id;
        $indices = [];
        $historial = [];
        $metas = [];
        $cargos = [];

        $metaId = $cronograma->historial->pluck(["meta_id"]);
        $cargoId = $cronograma->historial->pluck(["cargo_id"]);
        $plaza = request()->plaza ? 1 : 0;


        if ($meta_id) {
            $indices = $cronograma->historial()->orderBy('orden', 'ASC')->where("meta_id", $meta_id)->get();
        }else {
            $indices = $cronograma->historial()->orderBy('orden', 'ASC')->get();
        }


        if ($cargo_id) {
            $indices = $indices->where("cargo_id", $cargo_id);   
        }

        $indices = $indices->pluck(["id"]);
        $historial = Historial::with("work")->orderBy('orden', 'ASC')->whereIn("id", $indices);

        if ($meta_id) {
            $historial = $historial->where("meta_id", $meta_id);
        }

            
        if ($like) {
            if ($plaza) {
                $historial = $historial->whereHas('work', function($w) use($like) {
                    $w->where("plaza", "like", "%{$like}%");
                });
            }else {
                $historial = $historial->whereHas('work', function($w) use($like) {
                    $indice = is_numeric($like) ? 'numero_de_documento' : 'nombre_completo'; 
                    $w->where($indice, "like", "%{$like}%");
                });
            }
        }


        $historial = $historial->paginate(20);
        $metas = Meta::whereIn("id", $metaId)->get();
        $cargos = Cargo::whereIn("id", $cargoId)->get();

        return view('cronogramas.job', 
            compact(
                'cronograma', 'cargos', 'cargo_id', 'historial', 'like', 
                'metas', 'typeReports', 'indices', 'meta_id'
            ));
    }


    /**
     * Muestra un formulario para agregar a los trabajadores a un cronograma
     *
     * @param  string $slug
     * @return \Illuminate\View\View
     */
    public function add($id)
    {
        $cronograma = Cronograma::where("adicional", 1)
            ->where("pendiente", 0)
            ->findOrFail($id);

        $like = request()->query_search;
        $notIn = $cronograma->historial->pluck(['info_id']);

        $infos = Info::with(['work', 'cargo', 'categoria'])
            ->whereNotIn("id", $notIn)
            ->where("planilla_id", $cronograma->planilla_id)
            ->where('active', 1);

        if($like) {
            $infos->wherehas('work', function($w) use($like) {
                $w->where("nombre_completo", "like", "%{$like}%");
            });
        }

        return $infos->paginate(20);
    }


    /**
     * Almacena a los trabajadores agregador recientemente al cronograma
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addStore(Request $request, $id)
    {
        try {
            $cronograma = Cronograma::where('adicional', 1)
                ->where("estado", 1)
                ->where("pendiente", 0)
                ->findOrFail($id);
            // obtener infos
            $tmp_infos = $request->input('infos', []);
            // obtener infos
            $infos = Info::whereIn("id", $tmp_infos)->where('active', 1)->get();
            // insertar historial
            foreach ($infos as $info) {
                Historial::create([  
                    "work_id" => $info->work_id,
                    "info_id" => $info->id,
                    "planilla_id" => $info->planilla_id,
                    "cargo_id" => $info->cargo_id,
                    "categoria_id" => $info->categoria_id,
                    "meta_id" => $info->meta_id,
                    "fuente_id" => $info->fuente_id,
                    "sindicato_id" => $info->sindicato_id,
                    "afp_id" => $info->afp_id,
                    "type_afp_id" => $info->type_afp_id,
                    "numero_de_cussp" => $info->numero_de_cussp,
                    "fecha_de_afiliacion" => $info->fecha_de_afiliacion,
                    "banco_id" => $info->banco_id,
                    "numero_de_cuenta" => $info->numero_de_cuenta,
                    "numero_de_essalud" => $info->numero_de_essalud,
                    "plaza" => $info->plaza,
                    "perfil" => $info->perfil,
                    "escuela" => $info->escuela,
                    "pap" => $info->pap,
                    "cronograma_id" => $cronograma->id,
                    "afecto" => $info->afecto,
                    "orden" => substr($info->work->nombre_completo, 0, 2),
                    "ext_pptto" => $info->cargo->ext_pptto,
                    "total_desct" => 0
                ]);
            }
            // processar información
            ProssingRemuneracion::withChain([
                (new ProssingDescuento($cronograma, $tmp_infos))->onQueue('medium'),
                (new ProssingAportacion($cronograma, $tmp_infos))->onQueue('medium'),
            ])->dispatch($cronograma, $tmp_infos)
                ->onQueue('medium');

            return [
                "status" => true,
                "message" => "Los trabajadores están siendo procesados. Nosotros le notificaremos"
            ];

        } catch (\Throwable $th) {
            
            \Log::info($th);

            return [
                "status" => false,
                "message" => "Ocurrio un error al procesar la operación"
            ];
        }

    }



    public function estado(Request $request, $id) 
    {
        $cronograma = Cronograma::findOrFail($id);
        try {
            
            $cronograma->estado = $cronograma->estado ? 0 : 1;
            $cronograma->save();
            $message = $cronograma->estado ? 'activada' : 'desactivada';

            return [
                "status" => true,
                "message" => "Planilla {$message} correctamente!"
            ];

        } catch (\Throwable $th) {

            \Log::info($th);
            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación"
            ];

        }
    }


    public function destroyHistorial(Request $request, $id) 
    {
        $cronograma = Cronograma::where("estado", 1)->findOrFail($id);

        try {
            $historial = $request->input('historial', []);

            // eliminar remuneraciones
            Remuneracion::whereIn("historial_id", $historial)->delete();
            // eliminar descuentos 
            Descuento::whereIn("historial_id", $historial)->delete();
            // eliminar aportaciones
            Aportacion::whereIn("historial_id", $historial)->delete();
            // eliminar tasas educacionales
            Educacional::whereIn("historial_id", $historial)->delete();
            // eliminar historial
            Historial::whereIn("id", $historial)->delete();

            return [
                "status" => true,
                "message" => "Los trabajadores fuerón eliminados correctamente de la planilla"
            ];

        } catch (\Throwable $th) {
            
            \Log::info($th);

            return [
                "status" => false,
                "message" => "La operación falló" 
            ];

        }

    }



    public function vaciarDescuentos(Request $request, $id) 
    {
        $this->validate(request(), [
            "type_descuento_id" => "required",
            "monto" => "numeric"
        ]);

        $cronograma = Cronograma::with('historial')->where('estado', 1)->findOrFail($id);
        
        try {
            
            $type = $request->type_descuento_id;
            $monto = $request->monto;
            $isChange = Descuento::where('cronograma_id', $id)
                ->where('type_descuento_id', $type)
                ->update([ "monto" => $monto ]);
            // verificamos que se realizo los cambios
            if ($isChange) {
                $historial = $cronograma->historial;
                foreach ($historial as $history) {
                    DescuentoCollection::updateNeto($history);
                }
            }

            return [
                "status" => true,
                "message" => "Se vació correctamente!"
            ];

        } catch (\Throwable $th) {
            \Log::info($th);

            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación"
            ];
        }
        
    }


    public function createDescuentos(Request $request, $id)
    {
        $this->validate(request(), [
            "type_descuento_id" => "required",
            "monto" => "numeric"
        ]);

        $cronograma = Cronograma::where('estado', 1)->findOrFail($id);

        try {
            
            $type_id = $request->type_descuento_id;
            $monto = $request->monto;
            // verificamos si el type existe
            $types = TypeDescuento::where("activo", 1)
                ->where("id", $type_id)
                ->get();
            // obtenemos a todos los trabajadores que ya tiene el descuento agregado
            $isDescuento = Descuento::where('type_descuento_id', $type_id)->get();
            // agregar los descuentos a los trabajadores de la planilla
            $historial = Historial::where('cronograma_id', $cronograma->id)
                ->whereNotIn('historial_id', $isDescuento->pluck(['historial_id']))
                ->get();
            
            // crear descuento
            $collection = new DescuentoCollection($cronograma, $types);
            $collection->insert($historial, false);
            // actualizar nuevo historial
            foreach ($historial as $history) {
                DescuentoCollection::updateNeto($history);
            }
            
            return [
                "status" => true,
                "message" => "El descuento se agrego correctamente!"
            ];

        } catch (\Throwable $th) {
            \Log::info($th);

            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación"
            ];
        }
    }


}
