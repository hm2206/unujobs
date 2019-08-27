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
        $jobs = Work::orderBy('id', 'DESC');
        $like = request()->query_search;

        if($like) {
            $jobs = self::query($like, $jobs);
        }

        $jobs = $jobs->paginate(20);
        return view('trabajador.index', \compact('jobs'));
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
        $sindicatos = Sindicato::get(["id", "nombre"]);
        $bancos = Banco::get(["id", "nombre"]);
        $afps = Afp::get(["id", "nombre"]);
        $cargos = Cargo::with('categorias')->get(['id', 'descripcion']);
        $metas = Meta::all();
        $categorias = Categoria::whereHas('cargos', function($q) {
            $q->where("cargos.id", old('cargo_id'));
        })->get();

        if (!old('numero_de_documento')) {
            if($documento) {
                $essalud = new Essalud();
                $result = $essalud->search($documento);
            }
        }

        return view('trabajador.create', compact('documento', 'result', 'sindicatos', 'bancos', 'afps', 'categorias', 'cargos', 'metas'));
    }


    /**
     * Almacena un a nuevo trabajador
     *
     * @param  \App\Http\Requests\JobRequest  $request
     * @return \Illuminate\Http\Redirect
     */
    public function store(JobRequest $request)
    {
        $job = Work::create($request->except('descanso', 'afecto', 'cheque'));
        $job->nombre_completo = "{$job->ape_paterno} {$job->ape_materno} {$job->nombres}";
        $job->descanso = $request->input("descanso") ? 1 : 0;
        $job->afecto = $request->afecto ? 1 : 0;
        $job->cheque = $request->cheque ? 1 : 0;
        $job->save();
        return redirect()->route('job.index')->with(["success" => "El registro se guardo correctamente"]);
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
        $job->update($request->except('descanso', 'afecto', 'cheque'));
        $job->nombre_completo = "{$job->ape_paterno} {$job->ape_materno} {$job->nombres}";
        $job->descanso = $request->input("descanso") ? 1 : 0;
        $job->afecto = $request->afecto ? 1 : 0;
        $job->cheque = $request->cheque ? 1 : 0;
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


    /** 
     * Realiza busqueda del trabajador
     * @param  string  $like
     * @param  \Illuminate\Database\Eloquent\Builder  $job
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query($like, \Illuminate\Database\Eloquent\Builder $jobs) 
    {
        return $jobs->where("nombre_completo", "like", "%{$like}%")
            ->orWhere("numero_de_documento", "like", "%{$like}%");
    }


    /**
     * Muestra un formulario para visualizar y editar las remuneraciones del trabajador
     *
     * @param  string  $slug
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function remuneracion($id)
    {
        $work = Work::findOrFail($id);
        
        // configuración
        $year = request()->input('year', date('Y'));
        $mes = request()->input('mes', date('m'));
        $adicional = request()->adicional ? 1 : 0;
        $categoria_id = request()->categoria_id;
        $numero = request()->numero ? request()->numero : 1;
        
        // obtener planilla del trabajador
        $planilla_id = $work->infos->where("categoria_id", $categoria_id)
            ->pluck(['planilla_id']);

        // almacenar
        $total = 0;
        $dias = 30;
        $seleccionar = [];
        $cronograma = Cronograma::whereIn('planilla_id', $planilla_id)
            ->where('mes', $mes)
            ->where('año', $year)
            ->where("adicional", $adicional);

        if($adicional) {

            $seleccionar = $cronograma->get();
            $cronograma = $cronograma->where("numero", $numero);

        }

        $cronograma = $cronograma->firstOrFail();

        $remuneraciones = Remuneracion::with('typeRemuneracion')
            ->where("work_id", $id)
            ->where("categoria_id", $categoria_id)
            ->where("mes", $mes)
            ->where("año", $year)
            ->where("adicional", $adicional)
            ->where("sede_id", $work->sede_id)
            ->where("cronograma_id", $cronograma->id)
            ->get();

        $dias = $cronograma->dias;
        $total = round($remuneraciones->sum('monto'), 2);

        return [
            "cronograma" => $cronograma,
            "numeros" => $seleccionar,
            "remuneraciones" => $remuneraciones,
            "seleccionar" => $seleccionar,
            "dias" => $dias,
            "total" => $total,
            "adicional" => $adicional,
            "year" => $year,
            "mes" => $mes
        ];
        
    }



    /**
     * Almacena las remuneraciones del trabajador según el cronograma y el cargo
     *
     * @param  \Illuminate\Http\Request
     * @param  string  $id
     * @return  \Illuminate\Http\RedirectResponse
     */
    public function remuneracionUpdate(Request $request, $id)
    {

        $this->validate(request(), [
            "cronograma_id" => "required",
            "cargo_id" => "required",
            "categoria_id" => "required",
            "planilla_id" => "required"
        ]);

        try {
        
            $job = Work::findOrFail($id);
            $cronograma = Cronograma::where("estado", 1)
                ->findOrFail($request->cronograma_id);

            $remuneraciones = $job->remuneraciones
                ->where("cronograma_id", $cronograma->id)
                ->where("cargo_id", $request->cargo_id)
                ->where("planilla_id", $request->planilla_id)
                ->where("categoria_id", $request->categoria_id);

            $info = Info::where('active', 1)->where("work_id", $job->id)
                ->where("cargo_id", $request->cargo_id)
                ->where("categoria_id", $request->categoria_id)
                ->firstOrFail();

            $total = 0;

            foreach ($remuneraciones as $remuneracion) {
                if($cronograma->mes >= (int)date('m') && $cronograma->año == date('Y')) {
                    $tmp_remuneracion = $request->input($remuneracion->id);
                    if (is_numeric($tmp_remuneracion)) {
                        $remuneracion->monto = round($tmp_remuneracion, 2);
                        $remuneracion->save();
                        $total += $tmp_remuneracion;
                    }
                }else {
                    return [
                        "status" => false,
                        "message" => "Ocurrió un error al procesar la operación",
                        "total" => 0
                    ];
                }
            }


            // procesar los descuentos
            $collection = new WorkCollection($job);
            $collection->updateOrCreateDescuento($cronograma);
            

            // guardar monto total actual
            $info->update(["total" => round($total, 2)]);

            return [
                "status" => true,
                "message" => "Los datos se actualizarón correctamente!",
                "body" => round($total, 2)
            ];
        } catch (\Throwable $th) {
            \Log::info($th);
            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación",
                "total" => 0
            ];
        }

    }



    public function informacion($id) 
    {
        $infos = Info::where('active', 1)->with(["cargo", "planilla", "categoria", "meta"])
            ->where("work_id", $id)
            ->get();
        $work = Work::whereIn("id", $infos->pluck(['work_id']))->first();

        return [
            "infos" => $infos,
            "work" =>  $work,
            "afps" => Afp::all(),
            "bancos" => Banco::all()
        ];
    }

    /**
     * Muestra un formulario para visualizar y editar los descuentos del trabajador
     *
     * @param  string  $slug
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function descuento($id) 
    {

        $work = Work::findOrFail($id);

        // configuración
        $year = request()->input('year', date('Y'));
        $mes = request()->input('mes', date('m'));
        $adicional = request()->adicional ? 1 : 0;
        $categoria_id = request()->categoria_id;
        $numero = request()->numero ? request()->numero : 1;

        // obtener planilla del trabajador
        $planilla_id = $work->infos->where("categoria_id", $categoria_id)
            ->pluck(['planilla_id']);

        // almacenar
        $total = 0;
        $dias = 30;
        $base = 0;
        $seleccionar = [];
        $types = [];
        $cronograma = Cronograma::whereIn("planilla_id", $planilla_id)
                ->where('mes', $mes)
                ->where('año', $year)
                ->where("adicional", $adicional);


        if($adicional) {

            $seleccionar = $cronograma->get();
            $cronograma = $cronograma->where("numero", $numero);
        
        }
        
        $cronograma = $cronograma->firstOrFail();

        $descuentos = Descuento::with(['typeDescuento' => function($q){
                $q->orderBy("key", "ASC");
        }])->where("work_id", $id)
            ->where("categoria_id", $categoria_id)
            ->where("mes", $mes)
            ->where("año", $year)
            ->where("adicional", $adicional)
            ->where("sede_id", $work->sede_id)
            ->where("cronograma_id", $cronograma->id)
            ->get();

        $remuneraciones = Remuneracion::where("work_id", $id)
            ->where("categoria_id", $categoria_id)
            ->where("mes", $mes)
            ->where("año", $year)
            ->where("adicional", $adicional)
            ->where("sede_id", $work->sede_id)
            ->where("cronograma_id", $cronograma->id)
            ->get();

        $base = round($remuneraciones->where("base", 0)->sum('monto'), 2);
        $total = round($descuentos->where("base", 0)->sum('monto'), 2);
        $dias = $cronograma->dias;

        $total_neto =  round($remuneraciones->sum('monto') - $total, 2);

        $aportaciones = $descuentos->where("base", 1);

        return [
            "cronograma" => $cronograma,
            "numeros" => $seleccionar,
            "descuentos" => $descuentos,
            "aportaciones" => $aportaciones,
            "dias" => $dias,
            "total" => $total,
            "adicional" => $adicional,
            "year" => $year,
            "mes" => $mes,
            "base" => $base,
            "total_neto" => $total_neto
        ];
    }

    /**
     * Almacena los descuentos del trabajador según el cronograma y el cargo
     *
     * @param  \Illuminate\Http\Request
     * @param  string  $id
     * @return  \Illuminate\Http\RedirectResponse
     */
    public function descuentoUpdate(Request $request, $id)
    {
        $this->validate(request(), [
            "cronograma_id" => "required",
            "categoria_id" => "required",
            "categoria_id" => "required",
            "planilla_id" => "required"
        ]);
        
        try {
            
            $job = Work::findOrFail($id);
            $cronograma = Cronograma::where("estado", 1)
                ->find($request->cronograma_id);

            $remuneraciones = $remuneraciones = Remuneracion::where("work_id", $id)
                ->where("cronograma_id", $cronograma->id)
                ->where("categoria_id", $request->categoria_id)
                ->where("planilla_id", $request->planilla_id)
                ->where("cargo_id", $request->cargo_id)
                ->get();

            $descuentos = Descuento::where("work_id", $job->id)
                ->where("cronograma_id", $cronograma->id)
                ->where("categoria_id", $request->categoria_id)
                ->where("planilla_id", $request->planilla_id)
                ->where("cargo_id", $request->cargo_id)
                ->get();

            foreach ($descuentos as $descuento) {
                if($cronograma->mes >= (int)date('m') && $cronograma->año == date('Y')) {
                    $tmp_descuento = $request->input($descuento->id);
                    if (is_numeric($tmp_descuento) && $descuento->edit) {
                        $descuento->monto = round($tmp_descuento, 2);
                        $descuento->save();
                    }
                }else {
                    return [
                        "status" => false,
                        "message" => "Ocurrió un error al procesar la operación",
                        "body" => ""
                    ];
                }
            }

            $total = round($descuentos->where('base', 0)->sum('monto'), 2);
            $base = round($remuneraciones->where('base', 0)->sum('monto'), 2);
            $total_neto = round($remuneraciones->sum('monto') - $total, 2);

            return [
                "status" => true,
                "message" => "Los datos se actualizarón correctamente!",
                "body" => [
                    "total" => $total,
                    "total_neto" => $total_neto,
                    "base" => $base
                ]
            ];
        } catch (\Throwable $th) {
            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación",
                "body" => ""
            ];
        }
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
        $whereIn = $request->input('cronogramas', []);

        ReportBoletaWork::dispatch($work, $whereIn)
            ->onQueue('medium');

        return "";
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

        $work = Work::findOrFail($id);

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
                ->where("adicional", $adicional);


        if($adicional) {

            $seleccionar = $cronograma->get();
            $cronograma = $cronograma->where("numero", $numero);
        
        }
        
        $cronograma = $cronograma->firstOrFail();
        $detalles = Detalle::where("cronograma_id", $cronograma->id)
            ->orderBy("type_descuento_id", 'ASC')
            ->where("work_id", $work->id)
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


    public function observacion($id) 
    {
        $mes = request()->mes ? request()->mes : date('mes');
        $year = request()->year ? request()->year : date('Y');
        $adicional = request()->adicional ? 1 : 0;
        $numero = request()->numero ? request()->numero : 1;
        $seleccionar = [];

        $cronograma = Cronograma::where("mes", $mes)
            ->where("año", $year)
            ->where("adicional", $adicional);
            
        if ($adicional) {
            $seleccionar = $cronograma->get();
            $cronograma = $cronograma->where("numero", $numero);
        }

        $cronograma = $cronograma->firstOrFail();

        $observacion = DB::table("work_cronograma")
            ->where("cronograma_id", $cronograma->id)
            ->where("work_id", $id)
            ->select("observacion")
            ->first();

        $obs = isset($observacion->observacion) ? $observacion->observacion : '';

        return [
            "observacion" => $obs,
            "seleccionar" => $seleccionar,
            "cronograma" => $cronograma
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
