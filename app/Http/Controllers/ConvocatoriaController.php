<?php

namespace App\Http\Controllers;

use App\Models\Convocatoria;
use Illuminate\Http\Request;
use App\Http\Requests\ConvocatoriaRequest;

class ConvocatoriaController extends Controller
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
        return view("convocatoria.create");
    }


    public function store(ConvocatoriaRequest $request)
    {   
        $convocatoria = Convocatoria::create($request->all());
        return back()->with(["success" => "Los datos se guardaron correctamente!"]);
    }


    public function show(Convocatoria $convocatoria)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Convocatoria  $convocatoria
     * @return \Illuminate\Http\Response
     */
    public function edit(Convocatoria $convocatoria)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Convocatoria  $convocatoria
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Convocatoria $convocatoria)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Convocatoria  $convocatoria
     * @return \Illuminate\Http\Response
     */
    public function destroy(Convocatoria $convocatoria)
    {
        //
    }
}
