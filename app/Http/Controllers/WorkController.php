<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Work;
use App\Models\Info;


class WorkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query_search = request()->query_search;
        $planilla_id = request()->planilla_id;
        $infos = [];

        if (strlen($query_search) > 2) {
            $infos = Info::with(['work', 'categoria'])->where("planilla_id", $planilla_id)
            ->whereHas("work",function($w) use($query_search) {
                $w->where("nombre_completo", "like", "%{$query_search}%");
            })->get();
        }

        return $infos;
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
        //
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
        try {
            
            $work = Work::findOrFail($id);
            $work->update($request->except('descanso', 'afecto', 'cheque'));
            $work->nombre_completo = "{$work->ape_paterno} {$work->ape_materno} {$work->nombres}";
            $work->descanso = $request->input("descanso") ? 1 : 0;
            $work->afecto = $request->afecto ? 1 : 0;
            $work->cheque = $request->cheque ? 1 : 0;
            $work->save();
            return [
                "status" => true,
                "message" => "Los datos se actualizarón correctamente!"
            ];

        } catch (\Throwable $th) {

            \Log::info($th);
            return [
                "status" => false,
                "message" => "Ocurrió un error al actualizar los datos"
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
