<?php

namespace App\Http\Controllers;

use App\Models\Liquidar;
use Illuminate\Http\Request;
use App\Models\Work;

class LiquidarController extends Controller
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
            "work_id" => "required",
            "fecha_de_cese" => "required|date",
            "monto" => "required|numeric|max:99999"
        ]);

        try {
            
            $liquidar = Liquidar::create($payload);
            $work = Work::findOrFail($request->work_id);
            $work->update(["activo" => 0]);

            return [
                "status" => true,
                "message" => "El trabajador fué liquidado"
            ];
            
        } catch (\Throwable $th) {
            
            \Log::info($th);

            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la información"
            ];

        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Liquidar  $liquidar
     * @return \Illuminate\Http\Response
     */
    public function show(Liquidar $liquidar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Liquidar  $liquidar
     * @return \Illuminate\Http\Response
     */
    public function edit(Liquidar $liquidar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Liquidar  $liquidar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Liquidar $liquidar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Liquidar  $liquidar
     * @return \Illuminate\Http\Response
     */
    public function destroy(Liquidar $liquidar)
    {
        //
    }
}
