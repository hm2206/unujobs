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
            self::generarRemuneracion($cronograma);
            self::generarDescuento($cronograma);
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


    private function generarRemuneracion($cronograma)
    {
        $jobs = Work::where('planilla_id', $cronograma->planilla_id)->get();
        $types = TypeRemuneracion::all();

        foreach ($jobs as $job) {
            self::configurarRemuneracion($types,$cronograma, $job);
        }

    }

    public function generarDescuento($cronograma)
    {
        $jobs = Work::where('planilla_id', $cronograma->planilla_id)->get();
        $types = TypeDescuento::all();

        foreach ($jobs as $job) {
            self::configurarDescuento($types,$cronograma, $job);
        }
    }


    private function configurarRemuneracion($types, $cronograma, $job)
    {
        $mes = $cronograma->mes == 1 ? 12 : $cronograma->mes - 1;
        $year = $cronograma->mes == 1 ? $cronograma->año - 1 : $cronograma->año; 
        $hasRemuneraciones = Remuneracion::where("work_id", $job->id)
            ->where("mes", $mes)->where("año", $year)->get();
        if ($hasRemuneraciones->count() > 0) {
            foreach ($hasRemuneraciones as $remuneracion) {
                Remuneracion::create([
                    "work_id" => $job->id,
                    "categoria_id" => $job->categoria_id,
                    "cronograma_id" => $cronograma->id,
                    "type_remuneracion_id" => $remuneracion->id,
                    "mes" => $cronograma->mes,
                    "año" => $cronograma->año,
                    "monto" => $remuneracion->monto,
                    "adicional" => $cronograma->adicional
                ]);
            }
        }else {
            foreach ($types as $type) {
                $config = DB::table("concepto_type_remuneracion")
                    ->whereIn("concepto_id", $job->categoria->conceptos->pluck(["id"]))
                    ->where("categoria_id", $job->categoria->id)
                    ->where("type_remuneracion_id", $type->id)
                    ->get();
                $suma = $config->sum("monto");
                Remuneracion::create([
                    "work_id" => $job->id,
                    "categoria_id" => $job->categoria_id,
                    "cronograma_id" => $cronograma->id,
                    "type_remuneracion_id" => $type->id,
                    "mes" => $cronograma->mes,
                    "año" => $cronograma->año,
                    "monto" => $suma,
                    "adicional" => $cronograma->adicional
                ]);
            }
        }
    }


    private function configurarDescuento($types, $cronograma, $job)
    {
        $mes = $cronograma->mes == 1 ? 12 : $cronograma->mes - 1;
        $year = $cronograma->mes == 1 ? $cronograma->año - 1 : $cronograma->año; 
        $hasDescuentos = Descuento::where("work_id", $job->id)
            ->where("mes", $mes)->where("año", $year)->get();
        if($hasDescuentos->count() > 0) {
            foreach ($hasDescuentos as $descuento) {
                $suma = 0;
                Descuento::create([
                    "work_id" => $job->id,
                    "categoria_id" => $job->categoria_id,
                    "cronograma_id" => $cronograma->id,
                    "type_descuento_id" => $descuento->id,
                    "mes" => $cronograma->mes,
                    "año" => $cronograma->año,
                    "monto" => $descuento->monto,
                    "adicional" => $cronograma->adicional
                ]);
            }
        }else {
            foreach ($types as $type) {
                $suma = 0;
                Descuento::create([
                    "work_id" => $job->id,
                    "categoria_id" => $job->categoria_id,
                    "cronograma_id" => $cronograma->id,
                    "type_descuento_id" => $type->id,
                    "mes" => $cronograma->mes,
                    "año" => $cronograma->año,
                    "monto" => $suma,
                    "adicional" => $cronograma->adicional
                ]);
            }
        }
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
