<?php

namespace App\Http\Controllers;

use App\Models\Personal;
use Illuminate\Http\Request;
use App\Http\Requests\PersonalRequest;
use App\Models\Cargo;
use App\Models\Sede;
use App\Models\Dependencia;
use App\Models\Oficina;
use App\Models\Meta;
use App\Models\Question;
use \PDF;
use Illuminate\Support\Facades\Storage;

class PersonalController extends Controller
{

    public function index()
    {
        $personals = Personal::paginate(20);
        return view('personal.index', compact('personals'));
    }


    public function create()
    {
        $sedes = Sede::get(["id", "descripcion"]);
        $current_sede = [];
        $dependencias = [];
        $current_dependencia = [];
        $oficinas = [];
        $lugares = [];
        $metas = Meta::all();

        if($sedes) {
            $current_sede = old('sede_id') ? $sedes->find(old('sede_id')) : $sedes->first();
            if ($current_sede) {
                $dependencias = $current_sede->dependencias;
                $lugares = $current_sede->oficinas;
            }
        }

        if($dependencias) {
            $current_dependencia = old('dependencia_id') 
                ? $dependencias->find(old('dependencia_id')) 
                : $dependencias->first();

            if($current_dependencia) {
                $oficinas = Oficina::where("sede_id", $current_sede->id)
                    ->where("dependencia_id", old('dependencia_id'))
                    ->get();
            }

        }

        $cargos = Cargo::get(["id", "descripcion"]);

        return view("personal.create", \compact('sedes', 'dependencias', 'oficinas', 'cargos', 'lugares', 'metas'));
    }

    public function store(PersonalRequest $request)
    {

        return $request->all();

        $this->validate(request(), [
            "numero_de_requerimiento" => "unique:personals"
        ]);

        $bases = $request->input('bases', []);
        $requisitos = $request->input("requisitos", []);

        $personal = Personal::create($request->except(['aceptado', 'file', 'bases']));
        $personal->update(["bases" => json_encode($bases)]);

        foreach ($requisitos as $key => $requisito) {
            $titulo = isset($requisito[0]) ? $requisito[0] : "";
            $body = isset($requisito[1]) ? $requisito[1] : [];

            if($titulo) {
                Question::create([
                    "requisito" => $titulo,
                    "body" => json_encode($body),
                    "personal_id" => $personal->id
                ]);
            }

        }

        return back()->with(["success" => "EL registro se guardo correctamente!"]);
    }


    public function show($id)
    {
        $personal = Personal::findOrFail($id);
        return view("personal.show", \compact('personal'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Personal  $personal
     * @return \Illuminate\Http\Response
     */
    public function edit(Personal $personal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Personal  $personal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Personal $personal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Personal  $personal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Personal $personal)
    {
        //
    }

    public function aceptar(Request $request, $id)
    {
        $personal = Personal::findOrFail($id);
        $personal->update([
            "aceptado" => $personal->aceptado ? 0 : 1
        ]);

        return back();
    }


    public function pdf($id)
    {
        $personal = Personal::findOrFail($id);
        $bases = \json_decode($personal->bases);
        $pdf = PDF::loadView("pdf.requerimiento_personal", compact('personal', 'bases'));
        return $pdf->stream();
    }
}
