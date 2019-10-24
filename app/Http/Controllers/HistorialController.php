<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Historial;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Models\Obligacion;
use App\Models\Educacional;

class HistorialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Historial::paginate(20);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Historial  $historial
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Historial::with([
            'work', 'info', 'planilla', 'cargo',
            'cargo', 'categoria', 'meta', 'sindicato',
            'afp', 'type_afp'
        ])->findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Historial  $historial
     * @return \Illuminate\Http\Response
     */
    public function remuneracion($id)
    {
        $history = Historial::findOrFail($id);
        $remuneraciones = Remuneracion::with('typeRemuneracion')
            ->where('historial_id', $id)
            ->where("show", 1)
            ->get();

        return [
            "remuneraciones" => $remuneraciones,
            "total_bruto" => $history->total_bruto
        ];
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function descuento($id)
    {
        $history = Historial::findOrFail($id);
        $descuentos = Descuento::with('typeDescuento')->where('historial_id', $id)->get();

        return [
            "total_neto" => $history->total_neto,
            "base" => $history->base,
            "total_desct" => $history->total_desct,
            "descuentos" => $descuentos
        ];
    }


    /**
     * obtener las obligaciones
     *
     * @return \App\Models\Obligacion
     */
    public function obligacion($id)
    {
        return Obligacion::where('historial_id', $id)->get();
    }


    /**
     * obtener las obligaciones
     *
     * @return \App\Models\Obligacion
     */
    public function educacional($id)
    {
        return Educacional::with('type_educacional')->where('historial_id', $id)->get();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Historial  $historial
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Historial $historial)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Historial  $historial
     * @return \Illuminate\Http\Response
     */
    public function destroy(Historial $historial)
    {
        //
    }
}
