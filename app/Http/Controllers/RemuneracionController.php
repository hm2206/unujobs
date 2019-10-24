<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Historial;
use App\Models\Remuneracion;
use App\Models\Descuento;

class RemuneracionController extends Controller
{


    public function updateAll(Request $request, $id)
    {
        $historial = Historial::findOrFail($id);
        // obtenemos imformación de los nuevos montos
        $remuneraciones = \json_decode($request->input('remuneraciones'));
        // obtenemos las remuneraciones de la base de datos pero solo las editables
        $db_remuneraciones = Remuneracion::where('show', 1)
            ->where('edit', 1)
            ->where('historial_id', $historial->id)
            ->get();

        try {
            
            foreach ($remuneraciones as $remuneracion) {
                $current_remuneracion = $db_remuneraciones->find($remuneracion->id);
                if ($current_remuneracion) {
                    $monto = $remuneracion->monto ? $remuneracion->monto : 0;
                    $current_remuneracion->update(["monto" =>  \round($monto, 2) ]);
                }
            }


            $total_bruto = Remuneracion::where("historial_id", $historial->id)->where('show', 1)->sum('monto');
            $total_desct = Descuento::where('historial_id', $historial->id)->where('base', 0)->sum('monto');
            $base = Remuneracion::where('historial_id', $historial->id)->where('base', 0)->sum('monto');
            $total_neto = $total_bruto - $total_desct;
            // actualizar el estado del historial
            $historial->update([ 
                "total_bruto" => round($total_bruto, 2),
                "base" => round($base, 2),
                "total_desct" => round($total_desct, 2),
                "total_neto" => round($total_neto, 2)
            ]);

            return [
                "status" => true,
                "message" => "Los descuentos se registrarón correctamente!",
                "total_bruto" => round($total_bruto, 2)
            ];

        } catch (\Throwable $th) {
            
            \Log::info($th);

            return [
                "status" => false,
                "message" => "Algo salió",
                "total_bruto" => 0
            ];

        }

    }

}
