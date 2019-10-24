<?php

namespace App\Http\Controllers;

use App\Models\TypeEducacional;
use Illuminate\Http\Request;

class TypeEducacionalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TypeEducacional::all();
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
            "key" => "required",
            "descripcion" => "required",
            "type_descuento_id" => "required",
        ]);

        try {

            $type = TypeEducacional::create($request->all());
            
            return [
                "status" => true,
                "message" => "El registro fué exitoso!",
                "type_educacional" => $type,
            ];

        } catch (\Throwable $th) {
            return [
                "status" => false,
                "message" => "Ocurrio un error!",
                "type_educacional" => [],
            ];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TypeEducacional  $typeEducacional
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return TypeEducacional::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TypeEducacional  $typeEducacional
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $payload = $this->validate(request(), [
            "descripcion" => "required",
        ]);

        try {
            
            $type = TypeEducacional::findOrFail($id);
            $type->update($payload);

            return [
                "status" => true,
                "message" => "La actualización fué exitosa!",
                "type_educacional" => $type,
            ];

        } catch (\Throwable $th) {
            return [
                "status" => false,
                "message" => "Ocurrió un error al actualizar el registro",
                "type_educacional" => []
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TypeEducacional  $typeEducacional
     * @return \Illuminate\Http\Response
     */
    public function destroy(TypeEducacional $typeEducacional)
    {
        //
    }
}
