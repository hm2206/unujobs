<?php

namespace App\Http\Controllers;

use App\Models\Aportacion;
use App\Models\Historial;
use App\Collections\DescuentoCollection;
use App\Models\TypeAportacion;
use Illuminate\Http\Request;
use App\Models\Descuento;

class AportacionController extends Controller
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
            "type_aportacion_id" => "required",
            "historial_id" => "required"
        ]); 

        $type = TypeAportacion::findOrFail($request->type_aportacion_id);
        $history = Historial::findOrFail($request->historial_id);
        // cargar payload
        $payload = [
            "work_id" => $history->work_id,
            "info_id" => $history->info_id,
            "historial_id" => $history->id,
            "cronograma_id" => $history->cronograma_id,
            "type_descuento_id" => $type->type_descuento_id,
            "type_aportacion_id" => $type->id
        ];

        try {

            $aportacion = Aportacion::updateOrCreate($payload);
            $aportacion->porcentaje = $type->porcentaje;
            $aportacion->minimo = $type->minimo;
            $aportacion->default = $type->defualt;
            // calcular monto aportacion
            $monto = $history->base >= $type->minimo 
                ? ($history->base * $type->porcentaje) / 100 
                : $type->default;
            $aportacion->monto = \round($monto, 2);
            // actualizar aportacion
            $aportacion->save();
            // actualizamos descuento
            Descuento::where('historial_id', $history->id)
                ->where('type_descuento_id', $aportacion->type_descuento_id)
                ->update([ "monto" => round($aportacion->monto, 2) ]);

            return [
                "status" => true,
                "message" => "La aportación se agrego correctamente!"
            ];

        } catch (\Throwable $th) {

            \Log::info($th);

            return [
                "status" => false,
                "message" => "Ocurrió un error al agregar la aportación"
            ];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Aportacion  $aportacion
     * @return \Illuminate\Http\Response
     */
    public function show(Aportacion $aportacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Aportacion  $aportacion
     * @return \Illuminate\Http\Response
     */
    public function edit(Aportacion $aportacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Aportacion  $aportacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Aportacion $aportacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Aportacion  $aportacion
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $aportacion = Aportacion::findOrFail($id);
        try {
            Descuento::where('historial_id', $aportacion->historial_id)
                ->where('type_descuento_id', $aportacion->type_descuento_id)
                ->update(['monto' => 0]);
            // eliminar registro de aportación
            $aportacion->delete();
            return [
                "status" => true,
                "message" => "La aportación se quito correctamente!"
            ];
        } catch (\Throwable $th) {
            \Log::info($th);
            return [
                "status" => false,
                "message" => "Ocurrió un error al quitar la aportación :("
            ];
        }
    }
}
