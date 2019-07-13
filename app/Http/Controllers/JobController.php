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
use \PDF;

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


    public function update(Request $request, Work $job)
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
        $total = 0;

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
            $total = $remuneraciones->sum('monto');
        }
        
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

        $job->update(["total" => round($total, 2)]);
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

            $types = Remuneracion::where('work_id', $job->id)
                ->where("cronograma_id", $cronograma->id)
                ->where("base", 0)->get();

            $base = $types->sum('monto');
            $total = $descuentos->sum('monto');
        }

        $aporte = $base * 0.09;
        $aporte = $base < 930 ? 83.70 : $aporte;
        $total_neto = $job->total - $total;

        return view("trabajador.descuento", 
            compact('job', 'descuentos', 'cronograma', 'year', 'mes', 
            'seleccionar', 'adicional', 'numero', 'total', 'dias', 'base', 'aporte', 'total_neto'));
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
                    $descuento->monto = round($tmp_descuento, 2);
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

    public function boleta($id)
    {
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

}
