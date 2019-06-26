<?php

namespace App\Http\Controllers;

use App\Models\Cronograma;
use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Remuneracion;
use App\Models\Job;
use App\Models\planilla;

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
            ->where('planilla_id', $request->categoria_id)
            ->where('mes', $mes)
            ->where("adicional", $adicional)->get();

        if($cronogramas->count() > 0) {
            return back()->with(["danger" => "El cronograma ya existe"]);
        }

        $cronograma = Cronograma::create($request->except('adicional'));
        $cronograma->update([
            "mes" => $mes,
            "año" => $year,
            "adicional" => $adicional
        ]);

        self::generarRemuneracion($cronograma);

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
        $jobs = Job::where('planilla_id', $cronograma->planilla_id)->get();
        foreach ($jobs as $job) {
            foreach ($job->categoria->conceptos as $concepto) {
                Remuneracion::create([
                    "job_id" => $job->id,
                    "planilla_id" => $job->planilla_id,
                    "categoria_id" => $job->categoria_id,
                    "dias" => "30",
                    "concepto_id" => $concepto->id,
                    "cronograma_id" => $cronograma->id,
                    "sede_id" => $job->sede_id,
                    "mes" => $cronograma->mes,
                    "año" => $cronograma->año,
                    "monto" => $concepto->monto,
                    "adicional" => $cronograma->adicional,
                    "horas" => $concepto->horas
                ]);
            }
        }
    }

}
