<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Historial;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Models\Obligacion;
use App\Models\Educacional;
use App\Models\Planilla;
use App\Models\Cargo;
use App\Models\Categoria;
use App\Models\Cronograma;
use App\Collections\DescuentoCollection;
use App\Collections\RemuneracionCollection;
use App\Models\Aportacion;
use App\Tools\Helpers;
use App\Models\Info;
use App\Collections\BoletaCollection;

class HistorialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Historial::paginate(20);
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
     * @param  \App\Historial  $historial
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Historial::with([
            'work', 'info', 'planilla', 'cargo',
            'cargo', 'categoria', 'meta', 'sindicato',
            'afp', 'type_afp'
        ])->findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Historial  $historial
     * @return \Illuminate\Http\Response
     */
    public function remuneracion($id)
    {
        $history = Historial::findOrFail($id);
        $remuneraciones = Remuneracion::with('typeRemuneracion')
            ->where('historial_id', $id)
            ->where("show", 1)
            ->get();

        return [
            "remuneraciones" => $remuneraciones,
            "total_bruto" => $history->total_bruto
        ];
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function descuento($id)
    {
        $history = Historial::findOrFail($id);
        $descuentos = Descuento::with('typeDescuento')
            ->where('historial_id', $id)
            ->orderBy('orden', 'ASC')
            ->get();

        return [
            "total_neto" => $history->total_neto,
            "base" => $history->base,
            "total_desct" => $history->total_desct,
            "descuentos" => $descuentos
        ];
    }


    /**
     * obtener las obligaciones
     *
     * @return \App\Models\Obligacion
     */
    public function obligacion($id)
    {
        $history = Historial::findOrFail($id);
        return Obligacion::where('historial_id', $id)->get();
    }


    /**
     * obtener las obligaciones
     *
     * @return \App\Models\Obligacion
     */
    public function educacional($id)
    {
        $history = Historial::findOrFail($id);
        return Educacional::with('type_educacional')->where('historial_id', $id)->get();
    }


    public function aportacion($id) 
    {
        $history = Historial::findOrFail($id);
        return Aportacion::with('type_aportacion')->where('historial_id', $id)->get(); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Historial  $historial
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate(request(), [
            "cargo_id" => "required",
            "categoria_id" => "required",
            "meta_id" => "required",
            "perfil" => "required",
            "pap" => "required",
            "fecha_de_ingreso" => "required|date",
            "observacion" => "required|max:200"
        ]);

        $history = Historial::with('work', 'categoria')->findOrFail($id);
        $cronograma = Cronograma::where('estado', 1)->findOrFail($history->cronograma_id);
        $planilla = Planilla::findOrFail($history->planilla_id);
        $cargos = Cargo::whereHas('categorias', function($car) use($request) {
            $car->where('categorias.id', $request->categoria_id);
        })->with('categorias')->where('planilla_id', $planilla->id)->get();

        try {
            // verificar si el cargo existe en la planilla
            $isValido = $cargos->where("id", $request->cargo_id)->first();
            // almacenamos la plaza
            $plaza = $request->plaza;
            // verificar plaza
            $isPlaza = Helpers::historialPlazaDisponible($plaza, $history->id, $cronograma->mes, $cronograma->año);
            // verificamos que el usuario nos envia una plaza
            if ($plaza && !$isPlaza['success']) {
                return [
                    "status" => false,
                    "message" => $isPlaza['message']
                ];
            }
            
            if ($isValido) {
                // verificar si se realizó el cambio de categoria
                Helpers::changeCategoria($history, $cronograma, $request->categoria_id);
                // actualizar campos
                $history->update($request->except(['planilla_id']));
                $history->numero_de_cuenta = $request->numero_de_cuenta ? $request->numero_de_cuenta  : '';
                // actualizar afp
                DescuentoCollection::updateAFP($history);
                // actualizar sindicato
                DescuentoCollection::updateSindicato($history);
                // actualizar historial
                $history = DescuentoCollection::updateNeto($history);
                $history->save();
                // actualizar info actual
                Info::where('id', $history->info_id)->update($request->except(['planilla_id', 'observacion', '_method']));
                // responder al cliente
                return [
                    "status" => true,
                    "message" => "Los datos se actualizarón",
                    "body" => $history
                ];
            }

            throw new \Exception("El cargo o la categoria no son válidos", 1);

        } catch (\Throwable $th) {

            \Log::info($th);

            return [
                "status" => false,
                "message" => "algo salió mal",
                "body" => null
            ];  
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Historial  $historial
     * @return \Illuminate\Http\Response
     */
    public function destroy(Historial $historial)
    {
        //
    }


    public function boleta($id)
    {
        $historial = Historial::where('id', $id)->get();
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
