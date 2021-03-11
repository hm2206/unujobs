<?php

namespace App\Http\Controllers;

use App\Models\ConfigDescuento;
use Illuminate\Http\Request;

class ConfigDescuentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ConfigDescuento::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return [];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(request(),[
            "type_descuento_id" => "required",
            "porcentaje" => "required|numeric"
        ]);

        try {
            $config = ConfigDescuento::create($request->all());
            return [
                "status" => true,
                "message" => "El registro se actualizo correctamente",
                "body" => $config
            ];
        } catch (\Throwable $th) {
            return [
                "status" => false,
                "message" => "Ocurrió un error al realizar la oreración",
                "body" => $th
            ];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ConfigDescuento  $configDescuento
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ConfigDescuento::with('typeDescuento')->findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ConfigDescuento  $configDescuento
     * @return \Illuminate\Http\Response
     */
    public function edit(ConfigDescuento $configDescuento)
    {
        return [];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ConfigDescuento  $configDescuento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate(request(), [
            "type_descuento_id" => "required",
            "porcentaje" => "required|numeric"
        ]);

        try {
            $config = ConfigDescuento::findOrFail($id);
            $config->update($request->all());
            return [
                "status" => true,
                "message" => "El registro se actualizo correctamente",
                "body" => $config
            ];
        } catch (\Throwable $th) {
            return [
                "status" => false,
                "message" => "Ocurrió un error al realizar la oreración",
                "body" => $th
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ConfigDescuento  $configDescuento
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $config = ConfigDescuento::findOrFail($id);
            $config->delete();
            return [
                "status" => true,
                "message" => "El registro se eliminó correctamente",
                "body" => $config
            ];
        } catch (\Throwable $th) {
            return [
                "status" => false,
                "message" => "Ocurrió un error al realizar la oreración",
                "body" => $th
            ];
        }
    }
}
