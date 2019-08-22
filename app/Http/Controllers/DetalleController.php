<?php

namespace App\Http\Controllers;

use App\Models\Detalle;
use App\Models\Descuento;
use App\Models\Cronograma;
use Illuminate\Http\Request;

class DetalleController extends Controller
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
        $payload = $this->validate(request(), [
            "cronograma_id" => "required",
            "work_id" => "required",
            "type_detalle_id" => "required",
            "type_descuento_id" => "required",
            "categoria_id" => "required"
        ]);

        try {

            $detalle = Detalle::updateOrCreate($payload);
            // obtenemos los descuentos
            $descuento = Descuento::where("work_id", $detalle->work_id)
                ->where("cronograma_id", $detalle->cronograma_id)
                ->where("type_descuento_id", $detalle->type_descuento_id)
                ->where("categoria_id", $detalle->categoria_id)
                ->firstOrFail();
            // verificamos que el cronograma este activo
            $cronograma =  Cronograma::where("estado", 1)
                ->findOrFail($descuento->cronograma_id);
            // actualizamos el monto del detalle
            $detalle->monto = $request->monto;
            $detalle->save();
            // obtenemos y sumamos los montos de los detalles
            $tmp_monto = Detalle::where("work_id", $detalle->work_id)
                ->where("cronograma_id", $detalle->cronograma_id)
                ->where("type_descuento_id", $detalle->type_descuento_id)
                ->where("categoria_id", $detalle->categoria_id)
                ->sum("monto");
            // actualizamons el monto del descuento
            $descuento->update([
                "monto" => $tmp_monto,
                "edit" => 0
            ]);
            
            return [
                "status" => true,
                "message" => "Los datos se guardarón correctamente!",
                "body" => $detalle
            ];

        } catch (\Throwable $th) {
            
            \Log::info($th);
            return [
                "status" => false,
                "message" => "Ocurrio un error al procesar la operación",
                "body" => ""
            ];

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Detalle  $detalle
     * @return \Illuminate\Http\Response
     */
    public function show(Detalle $detalle)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Detalle  $detalle
     * @return \Illuminate\Http\Response
     */
    public function edit(Detalle $detalle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Detalle  $detalle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Detalle $detalle)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Detalle  $detalle
     * @return \Illuminate\Http\Response
     */
    public function destroy(Detalle $detalle)
    {
        //
    }

}
