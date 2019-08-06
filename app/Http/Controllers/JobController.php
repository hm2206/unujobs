<?php

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
use App\Jobs\ReportBoleta;
use App\Jobs\ReportCronograma;
use \PDF;

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

        $jobs = $jobs->paginate(10);
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
        $job = Work::create($request->all());
        $job->nombre_completo = "{$job->ape_paterno} {$job->ape_materno} {$job->nombres}";
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
        $job->update($request->all());
        $job->nombre_completo = "{$job->ape_paterno} {$job->ape_materno} {$job->nombres}";
        $job->save();
        return back()->with(["success" => "El registro se actualizo correctamente"]);
    }


    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Work $job)
    {
        return back();
    }


    /** 
     * Realiza
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
    public function remuneracion($slug)
    {
        //recuperar id
        $id = \base64_decode($slug);
        $job = Work::findOrFail($id);

        $categoria_id = \base64_decode(request()->categoria_id);

        $current = request()->categoria_id ? $job->infos->find($categoria_id) : $job->infos->first();
        $categorias = $job->infos;

        $year = request()->year ? (int)request()->year : date('Y');
        $mes = request()->mes ? (int)request()->mes : (int)date('m');
        $adicional = request()->adicional ? 1 : 0;
        $numero = request()->numero ? request()->numero : 1;
        $remuneraciones = [];
        $seleccionar = [];
        $dias = 30;
        $total = 0;

        if (!$current) {
            return back();
        }

        $selecionar = [];
        $cronograma = Cronograma::where('mes', $mes)
                ->where('año', $year)
                ->where("adicional", $adicional);
        
        if ($current) {
            $cronograma = $cronograma->where("planilla_id", $current->planilla_id);
        }


        if($adicional) {
            $seleccionar = $cronograma->get();
            $cronograma = $cronograma->where("numero", $numero);
        }

        $cronograma = $cronograma->first();

        if($cronograma) {

            if($current) {

                $remuneraciones = Remuneracion::where("work_id", $job->id)
                ->where('mes', $mes)
                ->where('año', $year)
                ->where("categoria_id", $current->categoria_id)
                ->where("cargo_id", $current->cargo_id)
                ->where("cronograma_id", $cronograma->id)
                ->get();

                $dias = $cronograma->dias;
                $total = $remuneraciones->sum('monto');
            }

        }
        
        return view("trabajador.remuneracion", 
            compact(
                'job', 'categoria_id', 'remuneraciones', 'total', 
                'dias', 'mes', 'year', 'cronograma', 'numero', 'seleccionar',
                'current', 'categorias'
            )
        );
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
            "categoria_id" => "required",
            "categoria_id" => "required",
            "planilla_id" => "required"
        ]);
        
        $job = Work::findOrFail($id);
        $cronograma = Cronograma::find($request->cronograma_id);
        $remuneraciones = $job->remuneraciones
            ->where("cronograma_id", $cronograma->id)
            ->where("cargo_id", $request->cargo_id)
            ->where("planilla_id", $request->planilla_id)
            ->where("categoria_id", $request->categoria_id);

        $info = Info::where("work_id", $job->id)
            ->where("cargo_id", $request->cargo_id)
            ->where("categoria_id", $request->categoria_id)
            ->firstOrFail();

        $total = 0;

        foreach ($remuneraciones as $remuneracion) {
            if($cronograma->mes == (int)date('m') && $cronograma->año == date('Y')) {
                $tmp_remuneracion = $request->input($remuneracion->id);
                if (is_numeric($tmp_remuneracion)) {
                    $remuneracion->monto = round($tmp_remuneracion, 2);
                    $remuneracion->save();
                    $total += $tmp_remuneracion;
                }
            }else {
                return back()->with(["danger" => "No es posible actualizar estos datos"]);
            }
        }

        $info->update(["total" => round($total, 2)]);
        return back();
    }


    /**
     * Muestra un formulario para visualizar y editar los descuentos del trabajador
     *
     * @param  string  $slug
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function descuento($slug) 
    {
        $id = \base64_decode($slug);
        $job = Work::findOrFail($id);

        $categoria_id = \base64_decode(request()->categoria_id);
        $seleccionar = [];

        $current = request()->categoria_id ? $job->infos->find($categoria_id) : $job->infos->first();
        $categorias = $job->infos;

        $year = request()->year ? (int)request()->year : date('Y');
        $mes = request()->mes ? (int)request()->mes : (int)date('m');
        $adicional = request()->adicional ? 1 : 0;
        $numero = request()->numero ? request()->numero : 1;
        $descuentos = [];
        $types = [];
        $dias = 30;
        $total = 0;
        $base = 0;

        if (!$current) {
            return back();
        }

        $cronograma = Cronograma::where('mes', $mes)
            ->where('año', $year)
            ->where("adicional", $adicional);

        if ($current) {
            $cronograma = $cronograma->where("planilla_id", $current->planilla_id);
        }

        if($adicional) {
            $seleccionar = $cronograma->get();
            $cronograma = $cronograma->where("numero", $numero);
        }

        $cronograma = $cronograma->first();

        if($cronograma) {
            $descuentos = Descuento::with('typeDescuento')->where("work_id", $job->id)
            ->where('mes', $mes)
            ->where('año', $year)
            ->where("categoria_id", $current->categoria_id)
            ->where("cargo_id", $current->cargo_id)
            ->where("cronograma_id", $cronograma->id)
            ->get();

            $types = Remuneracion::where('work_id', $job->id)
                ->where("cronograma_id", $cronograma->id)
                ->where("categoria_id", $current->categoria_id)
                ->where("cargo_id", $current->cargo_id)
                ->where("base", 0)->get();

            $base = $types->sum('monto');
            $total = $descuentos->sum('monto');
            $dias = $cronograma->dias;
        }

        $aporte = $base * 0.09;
        $aporte = $base < 930 ? 83.70 : $aporte;
        $total_neto = $current->total - $total;

        return view("trabajador.descuento", 
            compact('job', 'descuentos', 'cronograma', 'year', 'mes', 'categoria_id',
                'seleccionar', 'adicional', 'numero', 'total', 'dias', 'base', 'aporte', 
                'total_neto', 'current', 'categorias'
            ));
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

        $job = Work::findOrFail($id);
        $cronograma = Cronograma::find($request->cronograma_id);
        $descuentos = Descuento::where("work_id", $job->id)
            ->where("cronograma_id", $cronograma->id)
            ->where("categoria_id", $request->categoria_id)
            ->where("planilla_id", $request->planilla_id)
            ->where("cargo_id", $request->cargo_id)
            ->get();

        foreach ($descuentos as $descuento) {
            if($cronograma->mes == (int)date('m') && $cronograma->año == date('Y')) {
                $tmp_descuento = $request->input($descuento->id);
                if (is_numeric($tmp_descuento)) {
                    $descuento->monto = round($tmp_descuento, 2);
                    $descuento->save();
                }
            }else {
                return back()->with(["danger" => "No es posible actualizar estos datos"]);
            }
        }

        return back();
    }


    /**
     * Muestra un formulario para visualizar, crear y editar las obligaciones judiciales del trabajador
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function obligacion($slug)
    {
        $id = \base64_decode($slug);
        $job = Work::findOrFail($id);


        return view("trabajador.obligacion", 
            compact('job'));
    }


    /**
     * Muestra un panel de checkbox para generar las boletas de todos los cronogramas seleccionados
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function boleta($slug)
    {
        //recuperar id
        $id = \base64_decode($slug);
        $work = Work::findOrFail($id);
        $remuneraciones = Remuneracion::where('work_id', $work->id)->get();
        $cronogramas = Cronograma::whereIn("id", $remuneraciones->pluck(['cronograma_id']))
            ->orderBy('id', 'ASC')->paginate(30);

        $meses = [
            "Enero","Febrero","Marzo","Abril","Mayo","Junio",
            "Julio", "Agosto","Septiembre","Octubre","Noviembre","Diciembre"
        ];

        return view('trabajador.boleta', compact('work', 'cronogramas', 'meses'));
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
        $cronogramas = Cronograma::whereIn("id", $whereIn)->get();

        $cronogramas->map(function($q) use($work) {
            $remuneraciones = Remuneracion::where("work_id", $work->id)
                ->where("cronograma_id", $q->id)
                ->get();

            $descuentos = Descuento::with('typeDescuento')->where('work_id', $work->id)
                ->where("cronograma_id", $q->id)
                ->get();
            
            $q->remuneraciones = $remuneraciones;
            $q->descuentos = $descuentos->chunk(2)->toArray();
            $q->total_descuento = $descuentos->sum('monto');


            //base imponible
            $q->base = $remuneraciones->where('base', 0)->sum('monto');

            //aportes
            $q->essalud = $q->base < 930 ? 83.7 : $q->base * 0.09;
            $q->accidentes = $work->accidentes ? ($base * 1.55) / 100 : 0;

            //total neto
            $q->neto = $work->total - $q->total_descuento;
            $q->total_aportes = $q->essalud + $q->accidentes;

            return $q;
        });

        // return $cronogramas;

        $pdf = PDF::loadView("pdf.boleta", compact('work', 'cronogramas'));
        $pdf->setPaper('a4', 'landscape')->setWarnings(false);
        return $pdf->stream("boleta - {$work->nombre_completo}");
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

        $work = Work::findOrFail($id);

        $info = Info::updateOrCreate([
            "work_id" => $work->id,
            "cargo_id" => $request->cargo_id,
            "categoria_id" => $request->categoria_id,
            "planilla_id" => $request->planilla_id,
            "meta_id" => $request->meta_id
        ]);

        $info->update($request->all());

        return redirect()->route('job.config', $work->slug());

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
        $work = Work::findOrFail($id);
        $sindicatos = $request->input('sindicatos', []);
        $work->sindicatos()->sync($sindicatos);
        return back();
    }

}
