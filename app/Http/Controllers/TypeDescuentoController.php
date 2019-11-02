<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TypeDescuento;
use App\Models\Historial;
use App\Models\Descuento;
use App\Models\Remuneracion;
use App\Models\Cronograma;

class TypeDescuentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TypeDescuento::where('activo', 1)->get();
    }

    
    public function updateAll(Request $request, $id)
    {
        $historial = Historial::findOrFail($id);
        // obtenemos los nuevos montos de los descuentos
        $cronograma = Cronograma::findOrFail($historial->cronograma_id);
        // verificar si la planilla esta cerrada
        if ($cronograma->estado == 0) {
            return [
                "status" => false,
                "message" => "La planilla se encuentra cerrada!"
            ];
        }
        // obtenemos los descuentos enviados desde el cliente
        $descuentos = \json_decode($request->input('descuentos'));
        // obtenemos los descuentos de la base de datos solo las editables
        $db_descuentos = Descuento::where('historial_id', $historial->id)
            ->where('edit', 1)->get();

        try {
            
            foreach ($descuentos as $descuento) {
                $current_descuento = $db_descuentos->find($descuento->id);
                if ($current_descuento) {
                    $monto = $descuento->monto ? $descuento->monto : 0;
                    $current_descuento->update([ "monto" => round($monto, 2) ]);
                }
            }

            // obtener el total bruto
            $total_bruto = Remuneracion::where('historial_id', $historial->id)
                ->where('show', 1)
                ->sum('monto');
            // obtener el total de descuento
            $total_desct = Descuento::where('historial_id', $historial->id)
                ->where('base', 0)
                ->sum('monto');
            // guardar el total neto
            $total_neto = $total_bruto - $total_desct;
            // actualizar estado del historial
            $historial->update([
                "total_neto" => round($total_neto, 2),
                "total_desct" => round($total_desct, 2)
            ]);

            return [
                "status" => true,
                "message" => "Las remuneraciones se registrarón correctamente!",
                "total_bruto" => round($total_bruto, 2),
                "total_desct"  => round($total_desct, 2),
                "total_neto" => round($total_neto, 2)
            ];

        } catch (\Throwable $th) {

            \Log::info($th);

            return [
                "status" => false,
                "message" => "Algo salió mal!"
            ];

        }

    }

}
