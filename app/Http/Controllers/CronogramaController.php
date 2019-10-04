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

        return view('cronogramas.index', 
            \compact(
                'cronogramas', 'categoria_id', 'mes', 'year'
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

        $mes = $request->mes < 13 && $request->mes > 0 ? (int)$request->mes : (int)date('m');
        $pendiente = 1;
        $current_mes = (int)date('m') + 1;

        /*if ($mes > $current_mes || $mes < $current_mes - 1) {
            return [
                "status" => false,
                "message" => "El mes no está permitido!"
            ];
        }*/

        $year = date('Y');
        $adicional = $request->adicional ? 1 : 0;


        $cronogramas = Cronograma::where('año', $year)
            ->where('planilla_id', $request->planilla_id)
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

            ProssingRemuneracion::withChain([
                (new ProssingDescuento($cronograma))->onQueue('high')
            ])->dispatch($cronograma)
            ->onQueue('high');

        }elseif($cronograma->adicional == 1) {
            $cronograma->update([
                "numero" => $cronogramas->count()
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
    public function edit($slug)
    {
        return back();
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
        $infos = [];
        $metas = [];
        $cargos = [];

        $metaId = $cronograma->infos->pluck(["meta_id"]);
        $cargoId = $cronograma->infos->pluck(["cargo_id"]);


        if ($meta_id) {
            $indices = $cronograma->infos->where("meta_id", $meta_id);
        }else {
            $indices = $cronograma->infos;
        }


        if ($cargo_id) {
            $indices = $indices->where("cargo_id", $cargo_id);   
        }

        $indices = $indices->pluck(["id"]);
        $infos = Info::with("work")->whereIn("id", $indices);

        if ($meta_id) {
            $infos = $infos->where("meta_id", $meta_id);
        }

            
        if ($like) {
            $infos = $infos->whereHas('work', function($w) use($like) {
                $indice = is_numeric($like) ? 'numero_de_documento' : 'nombre_completo'; 
                $w->where($indice, "like", "%{$like}%");
            });
        }


        $infos = $infos->paginate(20);
        $metas = Meta::whereIn("id", $metaId)->get();
        $cargos = Cargo::whereIn("id", $cargoId)->get();

        return view('cronogramas.job', 
            compact(
                'cronograma', 'cargos', 'cargo_id', 'infos', 'like', 
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
        $notIn = $cronograma->infos->pluck(["id"]);

        $infos = Info::with(['work', 'cargo', 'categoria'])
            ->whereNotIn("id", $notIn)
            ->where("planilla_id", $cronograma->planilla_id);

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
            
            $tmp_infos = $request->input('infos', []);

            $infos = Info::whereIn("id", $tmp_infos)->get();

            $cronograma->infos()->syncWithoutDetaching($infos->pluck(["id"]));
            // procesamos las remuneraciones y los descuentos
            ProssingRemuneracion::withChain([
                (new ProssingDescuento($cronograma))->onQueue('high')
            ])->dispatch($cronograma)->onQueue('high');

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


}
