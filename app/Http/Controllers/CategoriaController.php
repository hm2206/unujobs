<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Concepto;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{

    public function index()
    {
        $categorias = Categoria::paginate(20);
        return view("categorias.index", \compact('categorias'));
    }


    public function create()
    {
        return view('categorias.create');
    }

 
    public function store(Request $request)
    {
        $this->validate(request(), [
            "nombre" => "required"
        ]);

        $categoria = Categoria::create($request->all());
        return back()->with(["success" => "El registro se guardo correctamente"]);
    }


    public function show(Categoria $categoria)
    {
        //
    }


    public function edit($id)
    {
        $categoria = Categoria::findOrFail($id);
        return view("categorias.edit", compact('categoria'));
    }


    public function update(Request $request, Categoria $categoria)
    {
        $this->validate(request(), [
            "nombre" => "required"
        ]); 

        return back()->with(["success" => "El registro se actualizó correctamente"]);
    }


    public function destroy(Categoria $categoria)
    {
        //
    }


    public function concepto($id)
    {
        $categoria = Categoria::findOrFail($id);
        $notIn = $categoria->conceptos->pluck(["id"]);
        $conceptos = Concepto::whereNotIn("id", $notIn)->get();
        return view("categorias.concepto", compact('categoria', 'conceptos'));
    }

    public function conceptoStore(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);
        $concepto = Concepto::findOrFail($request->concepto_id);
        $monto = $request->monto ? $request->monto : $concepto->monto;

        $categoria->conceptos()->syncWithoutDetaching($concepto->id);
        $categoria->conceptos()->updateExistingPivot($concepto->id, ["monto" => $monto]);
        return back()->with(["success" => "El concepto se añadio correctamente"]);
    }

}
