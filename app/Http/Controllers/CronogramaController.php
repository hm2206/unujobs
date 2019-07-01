<?php

namespace App\Http\Controllers;

use App\Models\Cronograma;
use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Remuneracion;
use App\Models\Work;
use App\Models\planilla;
use App\Models\TypeRemuneracion;
use App\Models\TypeDescuento;
use App\Models\Descuento;
use App\Jobs\ProssingRemuneracion;
use App\Jobs\ProssingDescuento;
use \DB;

class CronogramaController extends Controller
{
 
    public function index()
    {
        $mes = request()->mes ? (int)request()->mes : (int)date('m');
        $year = request()->year ? (int)request()->year : (int)date('Y');
        $adicional = request()->adicional ? 1 : 0;
        $mes = $mes == 0 || $mes > 12 ? (int)date('m') : $mes;
        $year = $year > date('Y') ? date('Y') : $year;
        
        $cronogramas = Cronograma::where('mes', $mes)
            ->where('año', $year)
            ->where('adicional', $adicional)
            ->paginate(20);
        
        return view('cronogramas.index', \compact('cronogramas', 'categoria_id', 'mes', 'year', 'adicional'));
    }


    public function create()
    {
        $planillas = Planilla::all();
        return view("cronogramas.create", compact('planillas'));
    }


    public function store(Request $request)
    {
        $this->validate(request(), [
            "planilla_id" => "required"
        ]);

        $mes = (int)date('m');
        $year = date('Y');
        $adicional = $request->adicional ? 1 : 0;


        $cronogramas = Cronograma::where('año', $year)
            ->where('planilla_id', $request->planilla_id)
            ->where('mes', $mes)->get();

        if ($adicional == 0 && $cronogramas->count() > 0) {
            return back()->with(["danger" => "El cronograma ya existe"]);
        }elseif($adicional == 1 && $cronogramas->count() < 1) {
            return back()->with(["danger" => "El cronograma principal aun no está creado"]);
        }

        $cronograma = Cronograma::create($request->except('adicional'));
        $cronograma->update([
            "mes" => $mes,
            "año" => $year,
            "adicional" => $adicional
        ]);

        if($cronograma->adicional == 0) {
            ProssingRemuneracion::dispatch($cronograma);
            ProssingDescuento::dispatch($cronograma);
        }elseif($cronograma->adicional == 1) {
            $cronograma->update([
                "numero" => $cronogramas->count()
            ]);
        }

        return back()->with(["success" => "Los datos se guardarón correctamente"]);
    }


    public function show(Cronograma $cronograma)
    {
        //
    }


    public function edit(Cronograma $cronograma)
    {
        //
    }

 
    public function update(Request $request, Cronograma $cronograma)
    {
        //
    }


    public function destroy(Cronograma $cronograma)
    {
        //
    }


    public function job($id)
    {
        $cronograma = Cronograma::findOrFail($id);
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

    public function add($id)
    {
        $cronograma = Cronograma::where("adicional", 1)->findOrFail($id);
        $like = request()->query_search;
        $remuneraciones = Remuneracion::where("cronograma_id", $cronograma->id)->get();
        $notIn = $remuneraciones->pluck(["work_id"]);
        $jobs = Work::whereNotIn("id", $notIn);

        if($like) {
           $jobs->where("nombre_completo", "like", "%{$like}%");
        }

        $jobs = $jobs->paginate(20);

        return view("cronogramas.add", compact('jobs', 'cronograma', 'like'));
    }


    public function addStore(Request $request, $id)
    {
        $cronograma = Cronograma::where('adicional', 1)->findOrFail($id);
        $tmp_jobs = $request->except(["_token"]);
        $jobs = Work::whereIn("id", $tmp_jobs)->get();
        $types = TypeRemuneracion::all();

        foreach ($jobs as $job) {
            self::configurarRemuneracion($types, $cronograma, $job);
        }

        return back()->with(["success" => "Los trabajadores fuerón agregados correctamente"]);

    }


}
