<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Historial;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Collections\RemuneracionCollection;
use App\Collections\DescuentoCollection;
use App\Models\Cronograma;

class RemuneracionController extends Controller
{


    public function updateAll(Request $request, $id)
    {
        $historial = Historial::findOrFail($id);
        // verificamos si se puede modificar
        $cronograma = Cronograma::findOrFail($historial->cronograma_id);
        // verificar si la planilla esta cerrada
        if ($cronograma->estado == 0) {
            return [
                "status" => false,
                "message" => "La planilla se encuentra cerrada!"
            ];
        }
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
                    $current_remuneracion->update([ "monto" =>  \round($monto, 2) ]);
                }
            }

            RemuneracionCollection::updateRelations($historial);
            $historial = RemuneracionCollection::updateNeto($historial);
            // actualizamos historial
            $historial->save();
            DescuentoCollection::updateAportaciones($historial);
            $historial = DescuentoCollection::updateAFP($historial);
            $historial = DescuentoCollection::updateNeto($historial);
            // actualizar el estado del historial
            $historial->save();
            return [
                "status" => true,
                "message" => "Los descuentos se registrarón correctamente!",
                "total_bruto" => $historial->total_bruto
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
