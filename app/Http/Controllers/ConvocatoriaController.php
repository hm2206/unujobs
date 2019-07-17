<?php

namespace App\Http\Controllers;

use App\Models\Convocatoria;
use Illuminate\Http\Request;
use App\Http\Requests\ConvocatoriaRequest;
use App\Models\Personal;

class ConvocatoriaController extends Controller
{

    public function index()
    {
        $convocatorias = Convocatoria::all();
        return view('convocatoria.index', compact('convocatorias'));
    }


    public function create()
    {
        $personals = Personal::where("aceptado", 1)
            ->where("fecha_final", "<=", date('Y-m-d'))
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


    public function edit(Convocatoria $convocatoria)
    {
        //
    }


    public function update(Request $request, Convocatoria $convocatoria)
    {
        //
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
}
