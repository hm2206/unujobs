<?php

namespace App\Http\Controllers;

use App\Models\Modulo;
use Illuminate\Http\Request;

class ModuloController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Modulo::with("modulos")
            ->where("modulo_id", null)
            ->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return [
            "status" => 404,
            "message" => "Ruta removida"
        ];
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
            "name" => "required|unique:modulos"
        ]);

        try {

            $token = \bcrypt(date('Y-m-d') . $request->name);

            $payload = [
                "name" => $request->name,
                "ruta" => $request->ruta ? url($request->ruta) : '',
                "modulo_id" => $request->modulo_id,
                "icono" => $request->icono,
                "token" => $token 
            ];

            return [
                "status" => true,
                "message" => "Los datos se guardarón correctamente!",
                "body" => Modulo::create($payload)
            ];

        } catch (\Throwable $th) {
        
            \Log::info($th);

            return [
                "status" => false,
                "message" => "Ocurrio un error al procesar la operación!",
                "body" => ""
            ];

        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Modulo  $modulo
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Modulo::with("modulos")->findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Modulo  $modulo
     * @return \Illuminate\Http\Response
     */
    public function edit(Modulo $modulo)
    {
        return [
            "status" => 404,
            "message" => "Ruta removida"
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Modulo  $modulo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate(\request(), [
            "name" => "required|unique:modulos,id,". $request->id
        ]);

        try {
            $modulo = Modulo::findOrFail($id);

            $modulo->update([
                "ruta" => $request->ruta ? url($request->ruta) : "",
                "modulo_id" => $request->modulo_id,
                "name" => $request->name,
                "icono" => $request->icono
            ]);

            return [
                "status" => true,
                "message" => "Los datos se guardarón correctamente!",
                "body" => $modulo
            ];

        } catch (\Throwable $th) {

            \Log::info($th);

            return [
                "status" => false,
                "message" => "Ocurrio un error al procesar la operación!",
                "body" => ""
            ];

        }

        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Modulo  $modulo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Modulo $modulo)
    {
        return [
            "status" => 401,
            "message" => "Ruta no autorizada"
        ];
    }
}
