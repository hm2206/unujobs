<?php

namespace App\Http\Controllers;

use App\Models\Educacional;
use Illuminate\Http\Request;
use App\Models\TypeEducacional;
use App\Models\Descuento;
use App\Models\Historial;

class EducacionalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Educacional::with('type_educacional')->paginate(30);
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
            "type_educacional_id" => "required",
            "historial_id" => "required",
            "monto" => "numeric|max:1000"
        ]);
        
        $type = TypeEducacional::findOrFail($request->type_educacional_id);
        $history = Historial::findOrFail($request->historial_id);
        try {
            // actualizamos o creamos
            $educacional = Educacional::updateOrCreate([
                "type_educacional_id" => $type->id,
                "type_descuento_id" => $type->type_descuento_id,
                "work_id" => $history->work_id,
                "info_id" => $history->info_id,
                "historial_id" => $history->id,
                "cronograma_id" => $history->cronograma_id
            ]);
            // actualizamos el monto 
            $educacional->update([ "monto" => $request->monto ]);
            // obtenemos todas las taza educacionales
            $monto = Educacional::where("historial_id", $history->id)
                ->where("type_descuento_id", $educacional->type_descuento_id)
                ->sum("monto");
            // actualizamos el descuento
            Descuento::where("historial_id", $history->id)
                ->where("type_descuento_id", $educacional->type_descuento_id)
                ->update([ "monto" => $monto ]);
            // devolvemos los datos
            return [
                "status" => true,
                "message" => "Se agrego la taza educacional correctamente",
                "body" => $educacional
             ];
        } catch (\Throwable $th) {
            \Log::info($th);
            return [
                "status" => false,
                "message" => "Ocurrió un error al agregar la taza educacional",
                "body" => null
            ];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Educacional  $educacional
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Educacional  $educacional
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Educacional $educacional)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Educacional  $educacional
     * @return \Illuminate\Http\Response
     */
    public function destroy(Educacional $educacional)
    {
        //
    }


    /**
     * actualizar tasa educacional del historial.
     *
     * @param  \App\Educacional  $educacional
     * @return \Illuminate\Http\Response
     */
    public function historial(Request $request, $id)
    {
        $history = Historial::findOrFail($id);
        try {
            // guardamos la petición
            $educacionales = json_decode($request->educacionales);
            $db_educacionales = Educacional::where('historial_id', $history->id)->get();
            $monto_total = 0;

            foreach ($educacionales as $educacional) {
                $db_current = $db_educacionales->find($educacional->id);
                if ($db_current) {
                    $monto = round((double) $educacional->monto, 2 );
                    $db_current->update([ "monto",  $monto ]);
                    $monto_total += $monto;
                }
            }

            Descuento::where('historial_id', $history->id)
                ->whereIn('type_descuento_id', $db_educacionales->pluck(['type_descuento_id']))
                ->update( [ "monto" => $monto_total ]);

            return [
                "status" => true,
                "message" => "Se actualizó las tasa educacionales!"
            ];
        } catch (\Throwable $th) {

            \Log::info($th);

            return [
                "status" => false,
                "message" => "Ups, algo salió mal :(",
            ];
        }
    }

}
