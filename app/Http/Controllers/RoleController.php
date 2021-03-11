<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Role::with("modulos")->get();
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
            "key" => "required|unique:roles",
            "name" => "required"
        ]);

        try {
            
            $role = Role::create($request->all());

            $modulos = $request->input("modulos", []);
            $role->modulos()->sync($modulos);

            return [
                "status" => true,
                "message" => "EL registro fué exitoso",
                "body" => $role
            ];

        } catch (\Throwable $th) {
            
            \Log::info($th);

            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación",
                "body" => ""
            ];

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate(request(), [
            "name" => "required"
        ]);

        try {
            
            $role = Role::findOrFail($id);
            $role->update($request->except('key'));

            $modulos = $request->input("modulos", []);
            $role->modulos()->sync($modulos);

            return [
                "status" => true,
                "message" => "EL registro fué exitoso",
                "body" => $role
            ];

        } catch (\Throwable $th) {
            
            \Log::info($th);

            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación",
                "body" => ""
            ];

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
