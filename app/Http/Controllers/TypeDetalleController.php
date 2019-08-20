<?php

namespace App\Http\Controllers;

use App\Models\TypeDetalle;
use Illuminate\Http\Request;
use App\Models\TypeDescuento;
use App\Models\Descuento;

class TypeDetalleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TypeDetalle::with(['typeDescuento' => function($t) {
            $t->orderBy("type_descuentos.id", 'ASC');
        }])->get();
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
            "descripcion" => "required",
            "type_descuento_id" => "required"
        ]);

        try {
            
            $mes = date('m');
            $year = date('Y');
            $type_descuento = TypeDescuento::findOrFail($request->type_descuento_id);
            $type = TypeDetalle::create($request->all());
            $type_descuento->edit = 0;
            $type_descuento->save();
            $descuentos = Descuento::where("type_descuento_id", $type_descuento->id)
                ->where("mes", $mes)
                ->where("año", $year)
                ->update(["edit" => 0]);
            
            return [
                "status" => true,
                "message" => "Los datos se guardarón correctamente!"
            ];

        } catch (\Throwable $th) {
            
            \Log::info($th);
            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación"
            ];

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TypeDetalle  $typeDetalle
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return TypeDetalle::findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TypeDetalle  $typeDetalle
     * @return \Illuminate\Http\Response
     */
    public function edit(TypeDetalle $typeDetalle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TypeDetalle  $typeDetalle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate(request(), [
            "descripcion" => "required",
            "type_descuento_id" => "required"
        ]);

        try {
            
            $type = TypeDetalle::findOrFail($id);
            $type->update($request->all());
            return [
                "status" => true,
                "message" => "Los datos se guardarón correctamente!",
                "body" => $type
            ];

        } catch (\Throwable $th) {
            
            \Log::info($th);
            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación"
            ];
            
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TypeDetalle  $typeDetalle
     * @return \Illuminate\Http\Response
     */
    public function destroy(TypeDetalle $typeDetalle)
    {
        //
    }
}
