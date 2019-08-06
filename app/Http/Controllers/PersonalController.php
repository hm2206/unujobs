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
use \DB;

class PersonalController extends Controller
{
    /**
     * Muestra una lista de los requerimientos de personal
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $personals = Personal::orderBy('id', 'DESC')->paginate(20);
        return view('personal.index', compact('personals'));
    }

    /**
     * Muestra un formulario para crear un nuevo requerimiento de personal
     *
     * @return \Illuminate\View\View
     */
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

    /**
     * Almacena un requerimiento de personal recien creado
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PersonalRequest $request)
    {
        
        $bases = $request->input('bases', []);
        $requisitos = $request->input("requisitos", []);

        $personal = Personal::create($request->except(['aceptado', 'file', 'bases']));

        //crear o cambiar slug
        $slug = $personal->changeSlug($personal->cargo_txt, $personal->id);

        $personal->update([
            "bases" => json_encode($bases),
            "slug" => $slug
        ]);

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


    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        return back();
        //$personal = Personal::findOrFail($id);
        //return view("personal.show", \compact('personal'));
    }


    /**
     * Muestra un formulario para editar un requerimiento de personal
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function edit($slug)
    {
        $sedes = Sede::all();
        $metas = Meta::all();
        $personal = Personal::where("slug", $slug)->firstOrFail();
        $questions = [];

        foreach ($personal->questions as $q) {

            array_push($questions, [
                $q->requisito,
                json_decode($q->body)
            ]);

        }

        return view("personal.edit", compact('personal', 'sedes', 'metas', 'questions'));
    }


    /**
     * Actualiza un requerimiento de personal recien modificado
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\Redirect
     */
    public function update(PersonalRequest $request, $slug)
    {
        $personal = Personal::where("slug", $slug)->firstOrFail();

        $bases = $request->input('bases', []);
        $requisitos = $request->input("requisitos", []);

        //actualizando slug
        $newSlug = $personal->changeSlug($personal->cargo_txt, $personal->id);

        $personal->update($request->except(['aceptado', 'file', 'bases']));
        $personal->update([
            "bases" => json_encode($bases),
            "slug" => $newSlug
        ]);


        DB::table('questions')->where('personal_id', $personal->id)->delete();

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

        return redirect()->route('personal.edit', $newSlug)->with(["success" => "EL registro se actualizó correctamente!"]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function destroy(Personal $personal)
    {
        return back();
    }

    /**
     * Actualiza el estado de la convocatoria  "aceptado" o "rechazado"
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\RedirectResponse
     */
    public function aceptar(Request $request, $slug)
    {
        $personal = Personal::where("slug", $slug)->firstOrFail();
        $personal->update([
            "aceptado" => $personal->aceptado ? 0 : 1
        ]);

        return back();
    }

    /**
     * Genera un archivo PDF del requerimiento de personal
     *
     * @param  string  $slug
     * @return \PDF
     */
    public function pdf($slug)
    {
        $personal = Personal::where("slug", $slug)->firstOrFail();
        $bases = \json_decode($personal->bases);
        $pdf = PDF::loadView("pdf.requerimiento_personal", compact('personal', 'bases'));
        return $pdf->stream();
    }

}
