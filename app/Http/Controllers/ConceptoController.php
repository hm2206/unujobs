<?php

namespace App\Http\Controllers;

use App\Models\Concepto;
use Illuminate\Http\Request;

class ConceptoController extends Controller
{

    public function index()
    {
        $conceptos = Concepto::all();
        return view('conceptos.index', compact('conceptos'));
    }

 
    public function create()
    {
        return view('conceptos.create');
    }


    public function store(Request $request)
    {
        $this->validate(request(), [
            "key" => "required",
            "descripcion" => "required"
        ]);

        $concepto = Concepto::create($request->all());
        return back()->with(["success" => "El registro se guardo correctamente"]);
    }


    public function show(Concepto $concepto)
    {
        return back();
    }


    public function edit($slug)
    {
        $id = \base64_decode($slug);
        $concepto = Concepto::findOrFail($id);
        return view('conceptos.edit', \compact('concepto'));
    }


    public function update(Request $request, $id)
    {
        $this->validate(request(), [
            "key" => "required",
            "descripcion" => "required"
        ]);

        $concepto = Concepto::findOrFail($id);
        $concepto->update($request->all());
        return back()->with(["success" => "Los registros fuerón actualizados correctamente"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Concepto  $concepto
     * @return \Illuminate\Http\Response
     */
    public function destroy(Concepto $concepto)
    {
        return back();
    }
}
