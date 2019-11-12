<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Work;
use App\Models\Info;
use App\Models\Cronograma;
use App\Jobs\ReportRenta;
use App\Models\Historial;
use App\Collections\RentaCollection;
use App\Collections\BoletaCollection;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Models\Aportacion;

class WorkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $like = request()->query_search;
        $works =  Work::orderBy('nombre_completo', 'ASC');
        
        if ($like) {
            $works = $works->where("nombre_completo", "like", "%{$like}%")
                ->orWhere("numero_de_documento", "like", "%{$like}%");
        }

        return $works->paginate(30);
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
        $info = Info::findOrFail($id);
        return Cronograma::whereIn("id", $info->cronogramas->pluck('id'))->paginate(30);
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
            $work->update($request->all());
            $work->nombre_completo = "{$work->ape_paterno} {$work->ape_materno} {$work->nombres}";
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


    public function historial($id)
    {
        return Historial::with('work', 'categoria', 'cargo', 'cronograma')
            ->where('work_id', $id)->paginate(30);
    }


    public function report(Request $request, $id) 
    {

        $work = Work::findOrFail($id);

        try {

            $historial = $request->input("historial", []);
            ReportRenta::dispatch($work, $historial)->onQueue('medium');
            
            return [
                "status" => true,
                "message" => "El reporte está siendo generado, vuelta más tarde!"
            ];

        } catch (\Throwable $th) {
            
            \Log::info($th);
            return [
                "status" => false,
                "messages" => "La operación falló"
            ];

        }
    }


    public function renta($id, $historialID = [])
    {
        $work = Work::findOrFail($id);
        $isHistorial = \explode(",", $historialID);
        $renta = new RentaCollection($work, $isHistorial);
        $renta->generate();
        return $renta->render();
    }


    public function boleta($id)
    {
        $historialID = explode(",", request()->input('historial', ""));
        $historial = Historial::whereIn('id', $historialID)->get();
        // remuneraciones
        $remuneraciones = Remuneracion::where('show', 1)
            ->whereIn('historial_id', $historial->pluck(['id']))
            ->get();
        // descuentos
        $descuentos = Descuento::whereIn('historial_id', $historial->pluck(['id']))->get();
        // aportaciones
        $aportaciones = Aportacion::whereIn("historial_id", $historial->pluck(['id']))->get();
        // generar boleta
        $boleta = BoletaCollection::init();
        $boleta->setRemuneraciones($remuneraciones);
        $boleta->setDescuentos($descuentos->where('base', 0));
        $boleta->setAportaciones($descuentos->where('base', 1));
        $boleta->setStyle('default', ['css', '/css/print/boleta-only.css']);
        $boleta->get($historial);
        return $boleta->view();
    }

}



