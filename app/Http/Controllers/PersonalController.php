<?php

namespace App\Http\Controllers;

use App\Models\Personal;
use Illuminate\Http\Request;
use App\Http\Requests\PersonalRequest;
use App\Models\Cargo;
use App\Models\Sede;
use App\Models\Dependencia;
use App\Models\Oficina;

class PersonalController extends Controller
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


    public function create()
    {
        $sedes = Sede::get(["id", "descripcion"]);
        $current_sede = [];
        $dependencias = [];
        $current_dependencia = [];
        $oficinas = [];
        $lugares = [];

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

        return view("personal.create", \compact('sedes', 'dependencias', 'oficinas', 'cargos', 'lugares'));
    }

    public function store(PersonalRequest $request)
    {
        $personal = Personal::create($request->all());
        return back()->with(["success" => "EL registro se guardo correctamente!"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Personal  $personal
     * @return \Illuminate\Http\Response
     */
    public function show(Personal $personal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Personal  $personal
     * @return \Illuminate\Http\Response
     */
    public function edit(Personal $personal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Personal  $personal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Personal $personal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Personal  $personal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Personal $personal)
    {
        //
    }
}
