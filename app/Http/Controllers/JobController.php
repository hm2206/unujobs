<?php
/**
 * Http/Controllers/JobController.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Http\Controllers;

use App\Models\Work;
use Illuminate\Http\Request;
use App\Tools\Reniec;
use App\Tools\Essalud;
use App\Http\Requests\JobRequest;
use App\Models\Banco;
use App\Models\Afp;
use App\Models\Sindicato;
use App\Models\Categoria;
use App\Models\Cargo;
use App\Models\Meta;
use App\Models\Remuneracion;
use App\Models\TypeRemuneracion;
use App\Models\Cronograma;
use App\Models\Descuento;
use App\Models\TypeDescuento;
use App\Models\Info;
use App\Models\Detalle;
use App\Jobs\ReportBoleta;
use App\Jobs\ReportBoletaWork;
use App\Jobs\ReportCronograma;
use App\Models\Planilla;
use \PDF;
use \DB;
use App\Collections\WorkCollection;

/**
 * Class JobController
 * 
 * @category Controllers
 */
class JobController extends Controller
{

    /**
     * Muestra una lista de los trabajadores
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // request para los filtros
        $estado = request()->estado != "" ? request()->estado : 1;
        $like = request()->query_search;
        $planilla_id = request()->input("planilla_id", "");
        $cargo_id = request()->input("cargo_id", "");
        $categoria_id = request()->input("categoria_id", "");
        $meta_id = request()->input("meta_id", "");
        $plaza = request()->plaza ? 1 : 0;

        // filtros
        $config = [
            "planilla" => $planilla_id,
            "cargo" => $cargo_id,
            "categoria" => $categoria_id,
            "meta" => $meta_id,
            "plaza" => $plaza,
            "total" => 0
        ];

        // ejecutar filtro
        $jobs = Info::with('work')->where('active', $estado);
        
        if ($config['planilla']) {
            $jobs = $jobs->where("planilla_id", $config['planilla']);
        }

        if ($config['cargo']) {
            $jobs = $jobs->where("cargo_id", $config['cargo']);
        }

        if ($config['categoria']) {
            $jobs = $jobs->where("categoria_id", $config['categoria']);
        }

        if ($config['meta']) {
            $jobs = $jobs->where("meta_id", $config['meta']);
        }

        if ($config['plaza']) {
            $jobs = $jobs->where("plaza","like", $like);
        }else {
            $jobs = $jobs->whereHas('work', function($w) use($like) {
                $w->where("nombre_completo", "like", "%{$like}%")
                    ->orWhere("numero_de_documento", "like", "%{$like}%")
                    ->orderBy('works.nombre_completo', 'DESC');
            });
        }

        // obteneos los id de las personas
        $index = $jobs->get(["id"]);
        $count = $index->count();

        // obtenos a los trabajadores
        $jobs = $jobs->paginate(20);

        return view('trabajador.index', \compact(
            'jobs', 'estado', 'planilla_id', 'cargo_id', 'categoria_id', 'meta_id',
            'count', "plaza"
        ));
    }

    /**
     * Muestra un formulario para crear un nuevo trabajador
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $documento = request()->input("documento", null);
        $result = (object)["success" => false, "message" => ""];

        if (!old('numero_de_documento')) {
            if($documento) {
                $essalud = new Essalud();
                $result = $essalud->search($documento);
            }
        }

        return view('trabajador.create', compact('documento', 'result'));
    }


    /**
     * Almacena un a nuevo trabajador
     *
     * @param  \App\Http\Requests\JobRequest  $request
     * @return \Illuminate\Http\Redirect
     */
    public function store(JobRequest $request)
    {
        $job = Work::create($request->all());
        $job->nombre_completo = "{$job->ape_paterno} {$job->ape_materno} {$job->nombres}";
        $job->save();
        return redirect()->route('job.show', $job->slug())->with(["success" => "El registro se guardo correctamente"]);
    }


    /**
     * Muestra a un trabajador específico
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        //recuperar id
        $id = \base64_decode($slug);
        $job = Work::findOrFail($id);
        return view("trabajador.show", \compact('job'));
    }

    /**
     * Muestra un formulario para editar a un trabajador
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function edit($slug)
    {
        //recuperando el id
        $id = \base64_decode($slug);
        $job = Work::findOrFail($id);

        $bancos = Banco::get(["id", "nombre"]);
        $afps = Afp::get(["id", "nombre"]);

        return view('trabajador.edit', compact('job', 'bancos', 'afps'));
    }

    /**
     * Actualiza a un trabajador recien modificado
     *
     * @param  \App\Http\Requests\JobRequest  $request
     * @param  \App\Models\Work  $job
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(JobRequest $request, Work $job)
    {
        $job->update($request->all());
        $job->nombre_completo = "{$job->ape_paterno} {$job->ape_materno} {$job->nombres}";
        $job->save();
        return back()->with(["success" => "El registro se actualizo correctamente"]);
    }


    /**
     * Uncomented
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Work $job)
    {
        return back();
    }

    

    public function informacion($id) 
    {
        $info = Info::with(["work", 'categoria'])->FindOrFail($id);
        return [
            "info" => $info,
            "work" =>  $info->work,
            "afps" => Afp::all(),
            "bancos" => Banco::all()
        ];
    }



    /**
     * Muestra un formulario para visualizar, crear y editar las obligaciones judiciales del trabajador
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function obligacion($id)
    {
        $work = Work::with('obligaciones')->findOrFail($id);
        
        // configuración
        $year = request()->input('year', date('Y'));
        $mes = request()->input('mes', date('m'));
        $adicional = request()->adicional ? 1 : 0;
        $categoria_id = request()->categoria_id;
        $numero = request()->numero ? request()->numero : null;
        $seleccionar = [];

        $cronograma = Cronograma::where('mes', $mes)
                ->where('año', $year)
                ->where("adicional", $adicional);

        if($adicional) {

            $seleccionar = $cronograma->get();
            $cronograma = $cronograma->where("numero", $numero);
        
        }
        
        $cronograma = $cronograma->firstOrFail();

        return [
            "work" => $work,
            "obligaciones" => $work->obligaciones->where("categoria_id", $categoria_id),
            "cronograma" => $cronograma,
            "numeros" => $seleccionar
        ];

    }


    /**
     * Muestra un panel de checkbox para generar las boletas de todos los cronogramas seleccionados
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function boleta($id)
    {
        $work = Work::findOrFail($id);
        $remuneraciones = Remuneracion::where('work_id', $work->id)->get();
        $cronogramas = Cronograma::with("planilla")->whereIn("id", $remuneraciones->pluck(['cronograma_id']))
            ->orderBy('id', 'DESC')->paginate(20);

        return $cronogramas;
    }

    /**
     * Genera un pdf de los cronogramas seleccionados recientemente en formato de boletas
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function boletaStore(Request $request, $id)
    {
        $work = Work::findOrFail($id);
        $inHistorial = $request->input('historial', []);

        ReportBoletaWork::dispatch($inHistorial, $work)->onQueue('medium');

        return "generando boleta";
    }


    /**
     * Muestra un formulario para configurar los cargos
     * Y un panel con checkbox para configurar los sindicatos
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function config($slug)
    {  
        //recuperar id del trabajador
        $id = \base64_decode($slug);
        $work = Work::with('infos')->findOrFail($id);
        $cargoNotIn = $work->infos->pluck(["id"]);
        $cargos = Cargo::whereNotIn("id", $cargoNotIn)->get();
        $sindicatos = Sindicato::all();
        $metas = Meta::all();
        $current = $cargos->find(request()->cargo_id);
        $categorias = [];

        if ($current) {
            $categorias = $current->categorias;
        }

        foreach ($sindicatos as $sindicato) {
            $sindicato->check = $work->sindicatos->where('id', $sindicato->id)->count() ? true : false;
        }

        return view('trabajador.configuracion', compact('work', 'cargos', 'categorias', 'current', 'sindicatos', 'metas'));
    }

    /**
     * Almacena la configuracion de los cargos del trabajador creados recientemente
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return  \illuminate\Http\RedirectResponse
     */
    public function configStore(Request $request, $id)
    {

        $this->validate(request(), [
            "cargo_id" => "required",
            "categoria_id" => "required",
            "meta_id" => "required",
            "planilla_id" => "required",
            "perfil" => "required"
        ]);

       try {

            $work = Work::findOrFail($id);

            $info = Info::updateOrCreate([
                "work_id" => $work->id,
                "cargo_id" => $request->cargo_id,
                "categoria_id" => $request->categoria_id,
                "planilla_id" => $request->planilla_id,
                "meta_id" => $request->meta_id
            ]);

            $info->update($request->all());

            $infos = Info::with(['planilla', 'cargo', 'categoria', 'meta'])
                ->where("work_id", $work->id)
                ->where("active", 1)->get();

            return [
                "status" => true,
                "message" => "El registro fué agregado correctamente!",
                "body" => $infos
            ];

       } catch (\Throwable $th) {
           
            \Log::info($th);

            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación",
                "body" => $th
            ];

       }

    }


    public function configDelete(Request $request, $id)
    {
        $this->validate(request(), [
            "info" => "required"
        ]);

        try {

            $work = Work::findOrFail($id);
            $info = Info::findOrFail($request->info);
            $info->active = 0;
            $info->save();

            return [
                "status" => true,
                "message" => "El registro eliminado correctamente!",
                "body" => $info
            ];

        } catch (\Throwable $th) {

            \Log::info($th);

            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación",
                "body" => $th
            ];

        }
    }

    
    /**
     * Almacena la configuracion de los sindicatos del trabajador agregados recientemente
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return  \illuminate\Http\RedirectResponse
     */
    public function sindicatoStore(Request $request, $id)
    {
        try {

            $work = Work::findOrFail($id);
            $sindicatos = $request->input('sindicatos', []);
            $work->sindicatos()->sync($sindicatos);

            return [
                "status" => true,
                "message" => "El registro fué agregado correctamente!",
                "body" =>  $work
            ];

        } catch (\Throwable $th) {

            \Log::info($th);

            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación",
                "body" => $th
            ];

        }
    }



    public function retencion($id)
    {
        $work = Work::findOrFail($id);
        $types = TypeDescuento::where("activo", 1)
            ->where("retencion", 1)
            ->orWhere("base", 1)
            ->get();
        
        foreach ($types as $type) {

            $tmp_type = $work->typeDescuentos->where("id", $type->id)->count();
            if ($tmp_type > 0) {
                $type->checked = true;
            }else {
                $type->checked = false;
            }

        }

        return $types;

    }



    public function retencionStore(Request $request, $id)
    {

        $work = Work::findOrFail($id);
        
        try {

            $retenciones = $request->input('retenciones', []);
            $work->typeDescuentos()->sync($retenciones);

            return [
                "status" => true,
                "message" => "Los datos se guardarón correctamente!"
            ];

        } catch (\Throwable $th) {
            
            \Log::info($th);

            return [
                "status" => false,
                "message" => "El registro fué eliminado correctamente!"
            ];

        }

    }


    public function detalle($id) 
    {

        $info = Info::findOrFail($id);

        // configuración
        $year = request()->input('year', date('Y'));
        $mes = request()->input('mes', date('m'));
        $adicional = request()->adicional ? 1 : 0;
        $categoria_id = request()->categoria_id;
        $numero = request()->numero ? request()->numero : 1;


        // almacenar
        $total = 0;
        $dias = 30;
        $base = 0;
        $observacion = "";
        $seleccionar = [];
        $types = [];
        $cronograma = Cronograma::where('mes', $mes)
                ->where('año', $year)
                ->where("adicional", $adicional)
                ->where("planilla_id", $info->planilla_id);


        if($adicional) {

            $seleccionar = $cronograma->get();
            $cronograma = $cronograma->where("numero", $numero);
        
        }
        
        $cronograma = $cronograma->firstOrFail();
        $detalles = Detalle::where("cronograma_id", $cronograma->id)
            ->orderBy("type_descuento_id", 'ASC')
            ->where("info_id", $info->id)
            ->get();

        

        $observacion = isset($observacion->observacion) ? $observacion->observacion : '';

        $dias = $cronograma->dias;

        return [
            "cronograma" => $cronograma,
            "numeros" => $seleccionar,
            "dias" => $dias,
            "adicional" => $adicional,
            "year" => $year,
            "mes" => $mes,
            "detalles" => $detalles,
        ];
    } 


    public function observacionUpdate(Request $request, $id)
    {
        try {
            $cronograma_id = $request->cronograma_id;
            $observacion = $request->observacion;
            $create = DB::table("work_cronograma")->where("cronograma_id", $cronograma_id)
                ->where("work_id", $id)
                ->update(["observacion" => $observacion]);

            return [
                "status" => true,
                "message" => "La observación se guardo correctamente!"
            ];
        } catch (\Throwable $th) {
            \Log::info($th);
            return [
                "status" => false,
                "message" => "Ocurrio un error al guardar la observación"
            ];
        }
    }
}
