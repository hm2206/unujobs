<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\Historial;
use App\Models\Descuento;
use App\Models\Remuneracion;
use App\Models\TypeRemuneracion;

class CalcPlanilla implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $cronograma;

    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($cronograma)
    {
        $this->cronograma = $cronograma;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // obtener cronograma
        $cronograma = $this->cronograma;
        // obtener a todos los trabajadores del cronograma actual
        $historial = Historial::with('categoria')->where("cronograma_id", $cronograma->id)->get();
        // obtener las remuneraciones
        $remuneraciones = Remuneracion::whereIn('historial_id', $historial->pluck(['id']))
            ->get();
        // obtener los descuentos
        $descuentos = Descuento::whereIn('historial_id', $historial->pluck(['id']))
            ->get();
        // obtenemos los types
        $types = TypeRemuneracion::where("activo", 1)->get();
        // bloqueamos las ediciones
        self::setEdit($historial, 0);
        // ejecutamos las las remuneraciones
        self::updateHistorial($historial, $types, $remuneraciones, $descuentos);
        // activamos la edición
        self::setEdit($historial, 1);
    }


    public function updateHistorial($historial, $types, $remuneraciones, $descuentos)
    {
        foreach ($historial as $history) {
            // actualizamos el al padre 
            self::calc_type_remuneracion($history, $types, $remuneraciones);
            // actualizamos el monto bruto
            $monto_bruto = $remuneraciones->where('historial_id', $history->id)
                ->where('show', 1)->sum('monto');
            // actualizamos la base imponible
            $base = $remuneraciones->where('historial_id', $history->id)
                ->where('show', 1)->where('base', 0)
                ->sum('monto');
            // actualizamos la base imponible para el plame
            $base_enc = self::calc_enc($history, $base);
            // actualizamos el total de descuentos
            $total_desct = $descuentos->where("base", 0)
                ->where("historial_id", $history->id)
                ->sum('monto');
            // actualizamos el historial
            $history->update([
                "total_bruto" => round($monto_bruto, 2),
                "base" => round($base, 2),
                "base_enc" => round($base_enc, 2),
                "total_desct" => round($total_desct, 2),
                "total_neto" => round($monto_bruto - $total_desct, 2)
            ]);
        }
    }


    private function calc_enc($history, $base)
    {
        // obtenemos la categoria del trabajador
        $categoria = $history->categoria;
        // verificamos si el trabajador solo tiene un solo concepto
        if ($categoria->conceptos->count() == 1 ) {
            // obtenemos los montos del los conceptos
            $montos = $categoria->conceptos->map(function($con) {
                return $con->config ? $con->config->monto : 0;
            });
            // sumamos los conceptos 
            return round($montos->sum(), 2);
        }
        // devolvemos la base por defecto
        return $base;
    }


    public function setEdit($historial, $condicion = 0)
    {
        // bloquear edición para los descuentos
        Descuento::whereIn("historial_id", $historial->pluck(['id']))
            ->update(['edit' => $condicion]);
        // bloquear edición para las remuneraciones
        Remuneracion::whereIn("historial_id", $historial->pluck(['id']))
            ->update(['edit' => $condicion ]);
    }


    private function calc_type_remuneracion($history, $types, $remuneraciones)
    {
        foreach ($types as $type) {
            // verificamos si tiene relación recursiva
            if ($type->type_remuneraciones->count() > 0) {
                // obtenemos los tipos recursivos
                $type_remuneraciones = $type->type_remuneraciones->pluck(['id']);
                // sumamos los montos recursivos
                $monto = $remuneraciones->whereIn('type_remuneracion_id', $type_remuneraciones)
                    ->where('historial_id', $history->id)
                    ->sum('monto');
                // actualizamos el padre
                $padre = Remuneracion::where('historial_id', $history->id)
                    ->where("type_remuneracion_id", $type->id)
                    ->update([ "monto" => round($monto, 2) ]);
                // next
                continue;
            }
        }
    }

}
