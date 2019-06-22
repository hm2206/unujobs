<?php

namespace App\Http\Controllers;

use App\Models\Job;
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

class JobController extends Controller
{

    public function index()
    {
        $jobs = Job::orderBy('id', 'DESC');
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
        $job = Job::create($request->all());
        $job->nombre_completo = "{$job->ape_paterno} {$job->ape_materno} {$job->nombres}";
        $job->save();
        return redirect()->route('job.index')->with(["success" => "El registro se guardo correctamente"]);
    }


    public function show(Job $job)
    {
        return view("trabajador.show", \compact('job'));
    }


    public function edit(Job $job)
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


    public function update(JobRequest $request, Job $job)
    {
        $job->update($request->all());
        $job->nombre_completo = "{$job->ape_paterno} {$job->ape_materno} {$job->nombres}";
        $job->save();
        return back()->with(["success" => "El registro se actualizo correctamente"]);
    }


    public function destroy(Job $job)
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
        $job = Job::findOrFail($id);
        $categoria = Categoria::findOrFail($job->categoria_id);
        $remuneraciones = Remuneracion::where("job_id", $job->id)
            ->where("categoria_id", $categoria->id)
            ->get();

        $total = 0;

        return view("trabajador.remuneracion", compact('job', 'categoria', 'remuneraciones', 'total'));
    }

}
