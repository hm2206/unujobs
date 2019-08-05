<?php

namespace App\Http\Controllers;

use App\Models\Obligacion;
use Illuminate\Http\Request;

class ObligacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(request(), [
            "beneficiario" => "required",
            "numero_de_documento" => "required",
            "numero_de_cuenta" => "required",
            "monto" => "required|numeric",
            "work_id" => "required"
        ]);

        Obligacion::create($request->all());

        return back()->with(["success" => "Los datos se guardarón correctamente"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Obligacion  $obligacion
     * @return \Illuminate\Http\Response
     */
    public function show(Obligacion $obligacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Obligacion  $obligacion
     * @return \Illuminate\Http\Response
     */
    public function edit(Obligacion $obligacion)
    {
        //
    }


    public function update(Request $request, $id)
    {
        $this->validate(request(), [
            "up_beneficiario" => "required",
            "up_numero_de_documento" => "required",
            "up_numero_de_cuenta" => "required",
            "up_monto" => "required|numeric",
            "up_work_id" => "required"
        ]);

        $obligacion = Obligacion::findOrfail($id);
        $obligacion->update([
            "beneficiario" => $request->input("up_beneficiario"),
            "numero_de_documento" => $request->input("up_numero_de_documento"),
            "numero_de_cuenta" => $request->input("up_numero_de_cuenta"),
            "monto" => $request->input("up_monto"),
            "work_id" => $request->input("up_work_id")
        ]);

        return back()->with(["update" => "Los datos se actualizarón correctamente!"]);
    }


    public function destroy($id)
    {
        $obligacion = Obligacion::findOrFail($id);
        $obligacion->delete();

        if (request()->ajax()) {
            return [
                "success" => "Los datos se eliminarón correctamente"
            ];
        }

        return back()->with(["success" => "Los datos se eliminarón correctamente"]);
    }
}
