<?php

namespace App\Http\Controllers;

use App\Models\Etapa;
use Illuminate\Http\Request;
use App\Models\TypeEtapa;
use \DB;

class EtapaController extends Controller
{

    public function index()
    {
        //
    }

 
    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $this->validate(request(), [
            "type_etapa_id" => "required",
            "convocatoria_id" => "required",
            "personal_id" => "required"
        ]);


        foreach ($request->input('postulantes', []) as $key => $pos) {

            $tmp_postulante = isset($request->postulantes[$key][0]) ? $request->postulantes[$key][0] : 0;
            $puntaje = isset($request->postulantes[$key][1]) ? $request->postulantes[$key][1] : 0;

            if($tmp_postulante) {

                $payload = [
                    "postulante_id" => $tmp_postulante,
                    "type_etapa_id" => $request->type_etapa_id,
                    "convocatoria_id" => $request->convocatoria_id,
                    "personal_id" => $request->personal_id
                ];

                $next = isset($request->nexts[$key][0]) ? 1 : 0;
    
                $etapa = Etapa::updateOrCreate($payload);
                $etapa->update([
                    "puntaje" => $puntaje,
                    "next" => $next,
                    "current" => 0
                ]);

    
                if ($etapa->next == 0) {

                    $etapas = DB::table('etapas')->where("postulante_id", $tmp_postulante)
                        ->where("personal_id", $etapa->personal_id)
                        ->orderBy('id', 'DESC')
                        ->where('id', '>', $etapa->id)
                        ->delete();

                }else {
                    $type = TypeEtapa::where("id", "<>", $request->type_etapa_id)->first();
                    
                    if ($type) {

                        $nuevo = Etapa::create([
                            "postulante_id" => $tmp_postulante,
                            "type_etapa_id" => $type->id,
                            "convocatoria_id" => $request->convocatoria_id,
                            "personal_id" => $request->personal_id
                        ]);

                        $nuevo->puntaje = 0;
                        $nuevo->current = 1;
                        $nuevo->save();
                    }

                }

            }

        }

        return back()->with(["Los datos se guardarón correctamente"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Etapa  $etapa
     * @return \Illuminate\Http\Response
     */
    public function show(Etapa $etapa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Etapa  $etapa
     * @return \Illuminate\Http\Response
     */
    public function edit(Etapa $etapa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Etapa  $etapa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Etapa $etapa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Etapa  $etapa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Etapa $etapa)
    {
        //
    }
}
