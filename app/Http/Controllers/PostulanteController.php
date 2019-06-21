<?php

namespace App\Http\Controllers;

use App\Models\Postulante;
use Illuminate\Http\Request;
use App\Tools\Reniec;
use App\Http\Requests\PostulanteRequest;

class PostulanteController extends Controller
{

    public function index()
    {
        //
    }

 
    public function create()
    {
        $documento = request()->input("documento", null);
        $exists = false;
        $result = (object)["success" => false, "message" => ""];

        if($documento) {
            $reniect = new Reniec();
            $result = $reniect->search($documento);
        }

        return view("postulante.create", \compact('exists', 'result'));
    }


    public function store(PostulanteRequest $request)
    {
        $postulante = Postulante::create($request->all());
        return back()->with(["success" => "Los datos se guardaron correctamente!"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Postulante  $postulante
     * @return \Illuminate\Http\Response
     */
    public function show(Postulante $postulante)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Postulante  $postulante
     * @return \Illuminate\Http\Response
     */
    public function edit(Postulante $postulante)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Postulante  $postulante
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Postulante $postulante)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Postulante  $postulante
     * @return \Illuminate\Http\Response
     */
    public function destroy(Postulante $postulante)
    {
        //
    }
}
