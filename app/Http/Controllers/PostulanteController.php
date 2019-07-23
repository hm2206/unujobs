<?php

namespace App\Http\Controllers;

use App\Models\Postulante;
use Illuminate\Http\Request;
use App\Tools\Reniec;
use App\Http\Requests\PostulanteRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

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
        $postulante = Postulante::where("numero_de_documento", $request->numero_de_documento)->first();

        if($postulante) {
            $postulante->update($request->all());
            $postulante->personals()->syncWithoutDetaching($request->personal_id);
        }else {
            $postulante = Postulante::create($request->all());
            $postulante->personals()->syncWithoutDetaching($request->personal_id);
        }

        if ($request->redirect) {
            $id = \base64_encode($postulante->id);
            $guard = Cache::put('postulante', $id, 60);
            return \redirect($request->redirect . "?postulante={$id}")->with(["success" => "Los datos se guardaron correctamente!"]);
        }

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


    public function upload(Request $request, $id)
    {
        $this->validate(request(), [
            "cv" => "required|mimes:pdf"
        ]);

        $postulante = Postulante::findOrFail($id);
        $file = $request->file('cv');
        $name = "cv_{$postulante->id}.pdf";
        $store = Storage::disk('public')->putFileAs("pdf/cv", $file, $name);
        $postulante->update(["cv" => "storage/" . $store]);

        return back()->with(["cv" => "EL  CV fué actualizado"]);
    }

}
