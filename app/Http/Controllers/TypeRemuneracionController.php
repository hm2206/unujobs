<?php

namespace App\Http\Controllers;

use App\Models\TypeRemuneracion;
use Illuminate\Http\Request;

class TypeRemuneracionController extends Controller
{

    public function index()
    {
        $remuneraciones = TypeRemuneracion::all();

        return view('remuneraciones.index', compact('remuneraciones'));
    }


    public function create()
    {
        return view('remuneraciones.create');
    }


    public function store(Request $request)
    {
        $payload = $this->validate(request(), [
            "key" => "required|unique:type_remuneracions",
            "descripcion" => "required",
        ]);
        
        $base = $request->base ? 1 : 0;

        $type = TypeRemuneracion::create($payload);
        $type->update(["base" => $base]);
        
        return back()->with(["success" => "Los datos se guardarón correctamente"]);
    }


    public function show(TypeRemuneracion $typeRemuneracion)
    {
        //
    }


    public function edit($id)
    {
        $remuneracion = TypeRemuneracion::findOrFail($id);

        return view('remuneraciones.edit', compact('remuneracion'));
    }


    public function update(Request $request, $id)
    {
        $this->validate(request(), [
            "key" => "required|unique:type_remuneracions,key,".$id,
            "descripcion" => "required",
        ]);

        $remuneracion = TypeRemuneracion::findOrFail($id);

        $base = $request->base ? 1 : 0;

        $remuneracion->update([
            "key" => $request->key,
            "descripcion" => $request->descripcion,
            "base" => $base
        ]);

        return back()->with(["success" => "Los datos se actualizarón correctamente"]);
    }


    public function destroy(TypeRemuneracion $typeRemuneracion)
    {
        //
    }
}
