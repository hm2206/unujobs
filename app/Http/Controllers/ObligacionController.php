<?php
/**
 * Http/Controllers/ObligacionController.php
 * 
 * @author Hans Medina <twd2206@gmail.com>
 */
namespace App\Http\Controllers;

use App\Models\Obligacion;
use App\Models\TypeDescuento;
use App\Models\Descuento;
use App\Models\Cronograma;
use App\Models\Historial;
use Illuminate\Http\Request;

/**
 * Class ObligacionController
 * 
 * @category Controllers
 */
class ObligacionController extends Controller
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
     * Almacena una obligacion recien creada
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate(request(), [
            "beneficiario" => "required",
            "numero_de_documento" => "required",
            "numero_de_cuenta" => "required",
            "monto" => "required|numeric",
            "work_id" => "required",
            "info_id" => "required",
            "historial_id" => "required",
            "cronograma_id" => "required"
        ]);

        try {
            // verificamos que el cronograma este activo
            $obligacion = Obligacion::create([
                "work_id" => $request->work_id,
                "info_id" => $request->info_id,
                "historial_id" => $request->historial_id,
                "cronograma_id" => $request->cronograma_id,
                "type_descuento_id" => 8, 
                "beneficiario" => $request->beneficiario,
                "numero_de_documento" => $request->numero_de_documento,
                "numero_de_cuenta" => $request->numero_de_cuenta,
                "monto" => $request->monto
            ]);
            // obtener los montos de las obligaciones
            $monto = Obligacion::where("historial_id", $request->historial_id)
                ->where("type_descuento_id", 8)
                ->sum("monto");
            // actualizar los descuentos
            Descuento::where('historial_id', $request->historial_id)
                ->where("type_descuento_id", 8)
                ->update([ "monto" => $monto, "edit" => 0 ]);
            // obtener historial
            $history = Historial::findOrFail($obligacion->historial_id);
            // obtener total bruto
            $total_bruto = $history->total_bruto;
            // obtener total de descuentos
            $total_desct = Descuento::where('historial_id', $history->id)
                ->where('type_descuento_id', $obligacion->type_descuento_id)
                ->sum('monto');
            // calculamos total neto
            $total_neto = $total_bruto - $total_desct;
            // actualizamos el historial
            $history->update([
                "total_desct" => round($total_desct, 2),
                "total_neto" => round($total_neto, 2)
            ]);
            // devolver los datos necesarios
            return [
                "status" => true,
                "message" => "Los datos se guardarón correctamente!",
                "body" => $obligacion
            ];

        } catch (\Throwable $th) {
            
            \Log::info($th);
            return [
                "status" => false,
                "message" => "Ocurrio un error al procesar la operación",
                "body" => "error"
            ];

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Obligacion  $obligacion
     * @return \Illuminate\Http\Response
     */
    public function show(Obligacion $obligacion)
    {
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Obligacion  $obligacion
     * @return \Illuminate\Http\Response
     */
    public function edit(Obligacion $obligacion)
    {
        return back();
    }

    /**
     * Actualiza una obligacion recien modificada
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->validate(request(), [
            "up_monto" => "required|numeric",
        ]);

        try {
            
            $obligacion = Obligacion::findOrFail($id);
            // actualizar el monto de obligacion actual
            $obligacion->update([
                "monto" => $request->input("up_monto")
            ]);
            // obtener los monto de las obligaciones
            $monto = Obligacion::where("historial_id", $obligacion->historial_id)
                ->where('type_descuento_id', $obligacion->type_descuento_id)
                ->sum("monto");
            // actualizar los descuentos
            Descuento::where("historial_id", $obligacion->historial_id)
                ->where("type_descuento_id", $obligacion->type_descuento_id)
                ->update([
                    "monto" => $monto,
                    "edit" => 0
                ]);
            // obtenemos el historial
            $history = Historial::findOrFail($obligacion->historial_id);
            // obtener el total bruto
            $total_bruto = $history->total_bruto;
            // obtenemos el total de descuentos
            $total_desct = Descuento::where('historial_id', $history->id)
                ->where('base', 0)
                ->sum('monto');
            // cacular el total neto
            $total_neto = $total_bruto - $total_desct;
            // actualizar historial
            $history->update([ 
                "total_desct" => round($total_desct, 2),
                "total_neto" => round($total_neto, 2)
            ]);

            return [
                "status" => true,
                "message" => "Los datos se guardarón correctamente!",
                "body" => $obligacion
            ];

        } catch (\Throwable $th) {
            
            \Log::info($th);

            return [
                "status" => false,
                "message" => "Ocurrio un error al procesar la operación",
                "body" => ""
            ];

        }

        return back()->with(["update" => "Los datos se actualizarón correctamente!"]);
    }

    /**
     * Remueve una obligacion específica
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            $obligacion = Obligacion::findOrFail($id);
            // obtenemos el historial
            $history = Historial::findOrFail($obligacion->historial_id);
            // eliminar obligacion
            $obligacion->delete();
            // volvemos a calcular el monto
            $monto = Obligacion::where("historial_id", $obligacion->historial_id)
                ->where("type_descuento_id", $obligacion->type_descuento_id)
                ->sum("monto");
            // actualizar los descuentos
            Descuento::where("historial_id", $obligacion->historial_id)
                ->where("type_descuento_id", $obligacion->type_descuento_id)
                ->update([
                    "monto" => $monto,
                    "edit" => 0
                ]);
            // obtener el total bruto
            $total_bruto = $history->total_bruto;
            // obtener total de descuentos
            $total_desct = Descuento::where('historial_id', $history->id)
                    ->where('type_descuento_id', $obligacion->id)
                    ->sum('monto');
            // calcular el total neto
            $total_neto = $total_bruto - $total_desct;
            // actutalizar historial
            $history->update([
                "total_desct" => round($total_desct, 2),
                "total_neto" => round($total_neto, 2)
            ]);

            return [
                "status" => true,
                "message" => "Los datos se eliminarón correctamente"
            ];

        } catch (\Throwable $th) {
            
            return [
                "status" => false,
                "message" => "Ocurrio un error al procesar la operación"
            ];

        }

    }
}
