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

class JobController extends Controller
{

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

        if($documento) {
            $essalud = new Essalud();
            $result = $essalud->search($documento);
        }

        return view('trabajador.create', compact('decumento', 'result', 'sindicatos', 'bancos', 'afps', 'categorias', 'cargos', 'metas'));
    }

 
    public function store(JobRequest $request)
    {
        $cargo = Cargo::findOrFail($request->cargo_id);
        $job = Work::create($request->all());
        $job->nombre_completo = "{$job->ape_paterno} {$job->ape_materno} {$job->nombres}";
        $job->planilla_id = $cargo->planilla_id;
        $job->save();
        return redirect()->route('job.index')->with(["success" => "El registro se guardo correctamente"]);
    }


    public function show(Work $job)
    {
        return view("trabajador.show", \compact('job'));
    }


    public function edit(Work $job)
    {
        $sindicatos = Sindicato::get(["id", "nombre"]);
        $bancos = Banco::get(["id", "nombre"]);
        $afps = Afp::get(["id", "nombre"]);
        $cargos = Cargo::with('categorias')->get(['id', 'descripcion']);
        $categorias = Categoria::whereHas('cargos', function($q) use($job) {
            if(old('cargo_id')) {
                $q->where("cargos.id", old('cargo_id'));
            }else{
                $q->where("cargos.id", $job->cargo_id);
            }   
        })->get();
        $metas = Meta::all();

        return view('trabajador.edit', compact('job','sindicatos', 'bancos', 'afps', 'cargos', 'categorias', 'metas'));
    }


    public function update(JobRequest $request, Work $job)
    {
        $job->update($request->all());
        $job->nombre_completo = "{$job->ape_paterno} {$job->ape_materno} {$job->nombres}";
        $job->save();
        return back()->with(["success" => "El registro se actualizo correctamente"]);
    }


    public function destroy(Work $job)
    {
        //
    }

    public function query($like, $jobs) 
    {
        return $jobs->where("nombre_completo", "like", "%{$like}%")
            ->orWhere("numero_de_documento", "like", "%{$like}%");
    }

    public function remuneracion($id)
    {
        $job = Work::findOrFail($id);
        $categoria = Categoria::findOrFail($job->categoria_id);

        $year = request()->year ? (int)request()->year : date('Y');
        $mes = request()->mes ? (int)request()->mes : (int)date('m');
        $adicional = request()->adicional ? 1 : 0;
        $numero = request()->numero ? request()->numero : 1;
        $remuneraciones = [];
        $dias = 30;

        $selecionar = [];
        $cronograma = Cronograma::where('mes', $mes)
            ->where('año', $year)
            ->where("planilla_id", $job->planilla_id)
            ->where("adicional", $adicional);

        if($adicional) {
            $seleccionar = $cronograma->get();
            $cronograma = $cronograma->where("numero", $numero);
        }


        $cronograma = $cronograma->first();

        if($cronograma) {
            $remuneraciones = Remuneracion::where("work_id", $job->id)
                ->where('mes', $mes)
                ->where('año', $year)
                ->where("categoria_id", $categoria->id)
                ->where("cronograma_id", $cronograma->id)
                ->get();

            $dias = $cronograma->dias;
        }
        

        $total = 0;

        foreach($remuneraciones as $remuneracion) {
            $total += $remuneracion->monto;
        }

        $job->update(["total" => $total]);
        
        return view("trabajador.remuneracion", 
            compact('job', 'categoria', 'remuneraciones', 'total', 'dias', 'mes', 'year', 'cronograma', 'numero', 'seleccionar')
        );
    }


    public function remuneracionUpdate(Request $request, $id)
    {
        $job = Work::findOrFail($id);
        $cronograma = Cronograma::find($request->cronograma_id);
        $cronograma->update(["dias" => $request->dias]);
        $remuneraciones = $job->remuneraciones->where("cronograma_id", $cronograma->id);

        foreach ($remuneraciones as $remuneracion) {
            if($cronograma->mes == (int)date('m') && $cronograma->año == date('Y')) {
                $tmp_remuneracion = $request->input($remuneracion->id);
                if (is_numeric($tmp_remuneracion)) {
                    $remuneracion->monto = $tmp_remuneracion;
                    $remuneracion->save();
                }
            }else {
                return back()->with(["danger" => "No es posible actualizar estos datos"]);
            }
        }

        return back();
    }

    public function descuento($id) 
    {
        $job = Work::findOrFail($id);

        $year = request()->year ? (int)request()->year : date('Y');
        $mes = request()->mes ? (int)request()->mes : (int)date('m');
        $adicional = request()->adicional ? 1 : 0;
        $numero = request()->numero ? request()->numero : 1;
        $descuentos = [];
        $types = [];
        $dias = 30;
        $total = 0;
        $base = 0;

        $cronograma = Cronograma::where('mes', $mes)
            ->where('año', $year)
            ->where("planilla_id", $job->planilla_id)
            ->where("adicional", $adicional);

        if($adicional) {
            $seleccionar = $cronograma->get();
            $cronograma = $cronograma->where("numero", $numero);
        }

        $cronograma = $cronograma->first();

        if($cronograma) {
            $descuentos = Descuento::with('typeDescuento')->where("work_id", $job->id)
            ->where('mes', $mes)
            ->where('año', $year)
            ->where("categoria_id", $job->categoria_id)
            ->where("cronograma_id", $cronograma->id)
            ->get();

            $dias = $cronograma->dias;

            $types = Remuneracion::where('work_id', $job->id)
                ->where("cronograma_id", $cronograma->id)
                ->where("base", 1)->get();

            $base = $types->sum('monto');
        }


        foreach($descuentos as $descuento) {
            $total += $descuento->monto;
        }

        $base = $base - $total;
        $base = $base == 0 ? $job->total : $base;
        $aporte = $base * 0.09;
        $aporte = $aporte < 930 ? 87.70 : $aporte;
        $total_neto = $job->total - $total;

        return view("trabajador.descuento", 
            compact('job', 'descuentos', 'cronograma', 'year', 'mes', 'seleccionar', 'adicional', 'numero', 'total', 'dias', 'base', 'aporte', 'total_neto'));
    }


    public function descuentoUpdate(Request $request, $id)
    {
        $job = Work::findOrFail($id);
        $cronograma = Cronograma::find($request->cronograma_id);
        $cronograma->update(["dias" => $request->dias]);
        $descuentos = Descuento::where("work_id", $job->id)
            ->where("cronograma_id", $cronograma->id)
            ->get();

        foreach ($descuentos as $descuento) {
            if($cronograma->mes == (int)date('m') && $cronograma->año == date('Y')) {
                $tmp_descuento = $request->input($descuento->id);
                if (is_numeric($tmp_descuento)) {
                    $descuento->monto = $tmp_descuento;
                    $descuento->save();
                }
            }else {
                return back()->with(["danger" => "No es posible actualizar estos datos"]);
            }
        }

        return back();
    }


    public function obligacion($id)
    {
        $job = Work::findOrFail($id);

        $year = request()->year ? (int)request()->year : date('Y');
        $mes = request()->mes ? (int)request()->mes : (int)date('m');
        $adicional = request()->adicional ? 1 : 0;
        $numero = request()->numero ? request()->numero : 1;
        $descuentos = [];
        $dias = 30;

        $cronograma = Cronograma::where('mes', $mes)
            ->where('año', $year)
            ->where("planilla_id", $job->planilla_id)
            ->where("adicional", $adicional);

        if($adicional) {
            $seleccionar = $cronograma->get();
            $cronograma = $cronograma->where("numero", $numero);
        }

        $cronograma = $cronograma->first();

        if($cronograma) {
            $descuentos = Descuento::with('typeDescuento')->where("work_id", $job->id)
            ->where('mes', $mes)
            ->where('año', $year)
            ->where("categoria_id", $job->categoria_id)
            ->where("cronograma_id", $cronograma->id)
            ->get();

            $dias = $cronograma->dias;
        }

        $total = 0;

        foreach($descuentos as $descuento) {
            $total += $descuento->monto;
        }

        return view("trabajador.obligacion", 
            compact('cronograma', 'year', 'mes', 'adicional', 'numero', 'dias', 'job', 'total', 'seleccionar'));
    }

}
