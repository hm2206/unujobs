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
use App\Models\TypeRemuneracion;
use App\Models\TypeDescuento;
use App\Models\Descuento;
use App\Jobs\ProssingRemuneracion;
use App\Jobs\ProssingDescuento;
use App\Jobs\ReportBoleta;
use App\Jobs\ReportCronograma;
use Illuminate\Support\Facades\Storage;
use \DB;

/**
 * Class CronogramaController
 * 
 * @category Controllers
 */
class CronogramaController extends Controller
{
    /**
     * Muestra una lista cronogramas de planillas
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $mes = request()->mes ? (int)request()->mes : (int)date('m');
        $year = request()->year ? (int)request()->year : (int)date('Y');
        $adicional = request()->adicional ? 1 : 0;
        $mes = $mes == 0 || $mes > 12 ? (int)date('m') : $mes;
        $year = $year > date('Y') ? date('Y') : $year;

        $categoria_id = "";
        
        $cronogramas = Cronograma::where('mes', $mes)
            ->where('año', $year)
            ->where('adicional', $adicional)
            ->paginate(20);

        //traer reportes
        $report_planilla_metas = "/storage/pdf/planilla_metas_{$mes}_{$year}_{$adicional}.pdf";
        $report_boletas = "/storage/pdf/boletas_{$mes}_{$year}_{$adicional}.pdf";


        return view('cronogramas.index', 
            \compact(
                'cronogramas', 'categoria_id', 'mes', 'year', 'adicional', 
                'report_planilla_metas', 'report_boletas'
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
            "dias" => "required",
        ]);

        $mes = (int)date('m');
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
            "adicional" => $adicional
        ]);

        if($cronograma->adicional == 0) {
            $jobs = Work::whereHas('infos', function($i) use($cronograma) {
                    $i->where("planilla_id", $cronograma->planilla_id);
                })->get();

            ProssingRemuneracion::dispatch($cronograma, $jobs);
            ProssingDescuento::dispatch($cronograma, $jobs);
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
    public function show(Cronograma $cronograma)
    {
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

        $cronograma = Cronograma::findOrFail($id);
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
        $cronograma = Cronograma::with("planilla")->findOrFail($id);
        $remuneraciones = Remuneracion::where('cronograma_id', $cronograma->id)->get();
        $jobs = [];
        $like = request()->query_search;

        if($remuneraciones->count() > 0) {
            $jobs = Work::whereIn("id", $remuneraciones->pluck(["work_id"]));
            if ($like) {
                $jobs = $jobs->where("nombre_completo", "like", "%{$like}%");
            }
            $jobs = $jobs->paginate(20);
        }

        return view('cronogramas.job', compact('jobs', 'cronograma', 'like'));
    }


    /**
     * Muestra un formulario para agregar a los trabajadores a un cronograma
     *
     * @param  string $slug
     * @return \Illuminate\View\View
     */
    public function add($id)
    {
        $cronograma = Cronograma::where("adicional", 1)->findOrFail($id);
        $like = request()->query_search;
        $notIn = $cronograma->works->pluck(["id"]);

        $jobs = Work::whereNotIn("id", $notIn)
            ->with(["infos" => function($q) {
                $q->with(["cargo", "categoria"]);
            }])->whereHas('infos', function($i) use($cronograma) {
                $i->where('infos.planilla_id', $cronograma->planilla_id);
            });

        if($like) {
           $jobs->where("nombre_completo", "like", "%{$like}%");
        }

        return $jobs->paginate(20);
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
        $this->validate(request(), [
            "jobs" => "required"
        ]);

        try {
            $cronograma = Cronograma::where('adicional', 1)->findOrFail($id);
            
            $tmp_jobs = $request->input('jobs', []);
            
            $cronograma->works()->syncWithoutDetaching($tmp_jobs);

            $jobs = Work::whereIn("id", $tmp_jobs)->get();

            ProssingRemuneracion::dispatch($cronograma, $jobs);
            ProssingDescuento::dispatch($cronograma, $jobs);
            
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


}
