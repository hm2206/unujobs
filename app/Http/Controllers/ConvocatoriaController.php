<?php

namespace App\Http\Controllers;

use App\Models\Convocatoria;
use Illuminate\Http\Request;
use App\Http\Requests\ConvocatoriaRequest;
use App\Models\Personal;
use App\Http\Requests\ConvocatoriaUpdateRequest;
use App\Models\Actividad;
use App\Models\TypeEtapa;
use App\Models\Postulante;
use \DB;
use \PDF;

class ConvocatoriaController extends Controller
{

    public function index()
    {
        $convocatorias = Convocatoria::all();
        return view('convocatoria.index', compact('convocatorias', 'personals'));
    }


    public function create()
    {
        $personals = Personal::where("aceptado", 1)
            ->where("fecha_final", ">=", date('Y-m-d'))
            ->get();
            
        return view("convocatoria.create", \compact('personals'));
    }


    public function store(ConvocatoriaRequest $request)
    {   
        $convocatoria = Convocatoria::create($request->except('aceptado'));
        return back()->with(["success" => "Los datos se guardaron correctamente!"]);
    }


    public function show(Convocatoria $convocatoria)
    {
        //
    }


    public function edit($id)
    {
        $convocatoria = Convocatoria::findOrFail($id);
        $personals = Personal::where("aceptado", 1)
            ->where("fecha_final", ">=", date('Y-m-d'))
            ->get();

        $actividades = [];

        foreach ($convocatoria->actividades as $actividad) {
            array_push($actividades, [
                $actividad->id, 
                $actividad->descripcion, 
                $actividad->fecha_inicio, 
                $actividad->fecha_final,
                $actividad->responsable
            ]);
        }

        return view('convocatoria.edit', \compact('convocatoria', 'personals', 'actividades'));
    }


    public function update(ConvocatoriaUpdateRequest $request, $id)
    {

        $convocatoria = Convocatoria::findOrFail($id);   
        $convocatoria->update($request->all());
        $whereNoIn = [];

        foreach ($convocatoria->personals as $per) {
            $per->update(["convocatoria_id" =>  0]);
        }

        $personals = $request->input('personals', []);

        //configurar personals
        foreach ($personals as $per) {
            $personal = Personal::find($per);
            if ($personal) {
                $personal->update(["convocatoria_id" => $convocatoria->id]);
            }
        }

        $activities = $request->input('activities', []);

        //configurar activities
        foreach ($activities as $activity) {
            $tmpID = isset($activity[0]) ? $activity[0] : "";
            $descripcion = isset($activity[1]) ? $activity[1] : "";
            $f_inicio = isset($activity[2]) ? $activity[2] : "";
            $f_final = isset($activity[3]) ? $activity[3] : "";
            $responsable = isset($activity[4]) ? $activity[4] : "";

            $actividad = Actividad::find($tmpID);

            if ($actividad) {
                $actividad->update([
                    "descripcion" => $descripcion,
                    "fecha_inicio" => $f_inicio,
                    "fecha_final" => $f_final,
                    "responsable" => $responsable,
                    "convocatoria_id" => $convocatoria->id
                ]);
            }else {
                $actividad = Actividad::create([
                    "descripcion" => $descripcion,
                    "fecha_inicio" => $f_inicio,
                    "fecha_final" => $f_final,
                    "responsable" => $responsable,
                    "convocatoria_id" => $convocatoria->id
                ]);
            }

            
            array_push($whereNoIn, $actividad->id);

        }


        //eliminar las actividades que ya no estan en el request
        DB::table("actividads")->whereNotIn("id", $whereNoIn)->delete();

        
        return back()->with(["success" => "Los datos se actualizarón correctamente"]);
    }


    public function destroy(Convocatoria $convocatoria)
    {
        //
    }


    public function aceptar(Request $request, $id)
    {
        $convocatoria = Convocatoria::findOrFail($id);
        $convocatoria->update([
            "aceptado" => $convocatoria->aceptado ? 0 : 1
        ]);

        return back();
    }

    public function pdf($id)
    {
        $convocatoria = Convocatoria::findOrFail($id);
        $year = isset(explode("-", $convocatoria->fecha_inicio)[0]) ? explode("-", $convocatoria->fecha_inicio)[0] : date('Y');
        $pdf = PDF::loadView('pdf.convocatoria', compact('convocatoria', 'year'));
        return $pdf->stream();
    }

    public function etapas($id)
    {
        $convocatoria = Convocatoria::findOrFail($id);
        $year = isset(explode("-", $convocatoria->fecha_final)[0]) ? explode("-", $convocatoria->fecha_final)[0] : date('Y');
        $etapas = TypeEtapa::all();

        $current = request()->personal ? $convocatoria->personals->find(request()->personal) : $convocatoria->personals->first();

        foreach ($etapas as $etapa) {
            $etapa->postulantes = Postulante::whereHas("etapas", function($e) use($current){
                            $e->where("personal_id", isset($current->id) ? $current->id : 0);
                        })->whereHas("etapas", function($e) use($etapa) {
                            $e->where("type_etapa_id", $etapa->id);
                        })->get(); 
        }

        return view("convocatoria.etapas", compact('convocatoria', 'etapas', 'year', 'current'));
    }
}
