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
use App\Models\Cronograma;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Models\TypeRemuneracion;
use App\Models\TypeDescuento;
use App\Collections\InfoCollection;
use App\Models\Obligacion;
use App\Models\Historial;

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
        if (request()->work_id) {
            return Info::with(
                'cargo', 
                'planilla', 
                'categoria', 
                'meta', 
                'sindicato',
                'type_aportaciones'
            )->where("work_id", request()->work_id)
            ->get();
        }
        return Info::paginate(10);
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
            "work_id" => "required",
            "planilla_id" => "required",
            "cargo_id" => "required",
            "categoria_id" => "required",
            "meta_id" => "required",
            "perfil" => "required",
            "pap" => "required",
            "fecha_de_ingreso" => "required",
            "afecto" => "required",
            "active" => "required"
        ]);

        try {

            $info = Info::create($request->all());
            $info->active = $request->active ? 1 : 0;
            $info->afecto = $request->afecto ? 1 : 0;
            $info->especial = $request->afecto ? 1 : 0;
            $info->save();

            return [
                "status" => true,
                "message" => "registro exitoso!",
                "body" => $info
            ];
        } catch (\Throwable $th) {
            return [
                "status" => false,
                "message" => "No se pudó guardar el registro!"
            ];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Info  $info
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $info = info::with(['type_aportaciones'])->findOrFail($id);
        return $info;
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
            "work_id" => "required",
            "planilla_id" => "required",
            "cargo_id" => "required",
            "categoria_id" => "required",
            "meta_id" => "required",
            "perfil" => "required",
            "pap" => "required",
            "fecha_de_ingreso" => "required",
            "afecto" => "required",
            "active" => "required"
        ]);

        try {

            $info = Info::findOrFail($id);
            $info->active = $request->active ? 1 : 0;
            $info->afecto = $request->afecto ? 1 : 0;
            $info->especial = $request->afecto ? 1 : 0;
            $info->save();

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


    public function type_aportacion(Request $request, $id)
    {
        $info = Info::findOrFail($id);
        $this->validate(request(), [
            "type_aportacion_id" => "required"
        ]);
        
        try {
            // agregar aportaciones
            $info->type_aportaciones()
                ->syncWithoutDetaching($request->type_aportacion_id);
            // responder al clente
            return [
                "status" => true,
                "message" => "La aportación se agrego correctamente!",
                "aportaciones" => $info->type_aportaciones
            ];
        } catch (\Throwable $th) {
            \Log::info($th);
            return [
                "status" => false,
                "message" => "Ocurrió un error al agregar la aportación",
                "aportaciones" => []
            ];
        }
    }


    public function delete_aportacion(Request $request, $id)
    {
        $info = Info::findOrFail($id);
        $this->validate(request(), [
            "type_aportacion_id" => "required"
        ]);
        
        try {
            // agregar aportaciones
            $info->type_aportaciones()
                ->detach($request->type_aportacion_id);
            // responder al clente
            return [
                "status" => true,
                "message" => "La aportación se elimino correctamente!",
                "aportaciones" => $info->type_aportaciones
            ];
        } catch (\Throwable $th) {
            \Log::info($th);
            return [
                "status" => false,
                "message" => "Ocurrió un error al eliminar la aportación",
                "aportaciones" => []
            ];
        }
    }


    public function historial($id)
    {
        $info = Info::findOrFail($id);
        return Historial::where('info_id', $info->id)
            ->orderBy('cronograma_id', 'DESC')
            ->with('categoria', 'work', 'cronograma', 'planilla')
            ->paginate();
    }

}
