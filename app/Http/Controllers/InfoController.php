<?php
/**
 * Http/Controllers/InfoController.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Http\Controllers;

use App\Models\Info;
use \DB;
use Illuminate\Http\Request;

/**
 * Class InfoController
 * 
 * @category Controllers
 */
class InfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return back();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return back();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Info  $info
     * @return \Illuminate\Http\Response
     */
    public function show(Info $info)
    {
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Info  $info
     * @return \Illuminate\Http\Response
     */
    public function edit(Info $info)
    {
        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Info  $info
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $this->validate(request(), [
            "planilla_id" => "required|max:20",
            "cargo_id" => "required|max:20",
            "categoria_id" => "required|max:20",
            "meta_id" => "required|max:20",
            "pap" => "required|max:2",
            "perfil" => "required|max:200",
        ]);

        try {

            $info = Info::findOrFail($id);
            $info->update($request->all());

            $cronograma_id = $request->cronograma_id;

            if ($cronograma_id) {
                $create = DB::table("work_cronograma")->where("cronograma_id", $cronograma_id)
                    ->where("work_id", $info->work_id)
                    ->update(["observacion" => $request->observacion]);
            }

            return [
                "status" => true,
                "message" => "Los datos se guardarón correctamente"
            ];

        } catch (\Throwable $th) {
            
            \Log::info($th);
            return [
                "status" => false,
                "message" => "Ocurrió un error"
            ];

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Info  $info
     * @return \Illuminate\Http\Response
     */
    public function destroy(Info $info)
    {
        return back();
    }
}
