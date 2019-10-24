<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\TypeAportacion;
use App\Models\Descuento;
use App\Models\Historial;
use App\Models\Aportacion;

class ProssingAportacion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;
    private $cronograma;
    private $historial;
    private $types;
    private $payload;
    private $infoIn = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($cronograma, $infoIn = [])
    {
        $this->cronograma = $cronograma;
        $this->infoIn = $infoIn;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // obtener las aportaciones para procesar
        $this->types = TypeAportacion::with('type_descuento')->get();
        // obtenemos a los trabajadores de la planilla actual que tengan configuración de aportación
        $this->historial = Historial::whereHas('info', function($i){
            $i->whereHas('type_aportaciones');
        })->with(['info' => function($i) {
            $i->with('type_aportaciones');
        }])->where('cronograma_id', $this->cronograma->id);
        // verificar si hay infos especificos
        if (count($this->infoIn)) {
            $this->historial = $this->historial->get();
        }
        // creamos storage para crear aportaciones
        $this->payload = collect();
        // configuramos las aportaciones
        foreach ($this->historial as $history) {
            // obtenemos el info actual
            $info = $history->info;
            // recorremos los type_aportacions
            foreach ($info->type_aportaciones as $aportacion) {
                // obtenemos la base imponible
                $base = $history->base;
                // calcular aportacion
                $monto = $base >= $aportacion->minimo ? ($base * $aportacion->porcentaje) / 100 : 0;
                // almacenar en el payload
                $this->payload->push([
                    "work_id" => $history->work_id,
                    "info_id" => $history->info_id,
                    "historial_id" => $history->id,
                    "cronograma_id" => $history->cronograma_id,
                    "type_descuento_id" => $aportacion->type_descuento_id,
                    "type_aportacion_id" => $aportacion->id,
                    "porcentaje" => $aportacion->porcentaje,
                    "minimo" => $aportacion->minimo,
                    "default" => $aportacion->default,
                    "monto" => round($monto)
                ]);
            }
        }   
        // realizamos la inserción masiva de las aportaciones
        foreach ($this->payload->chunk(1000)->toArray() as $insert) {
            Aportacion::insert($insert);
        }
        // actualizamos las aportaciones de los descuentos
        self::updateDescuentos();
    }



    public function updateDescuentos() {
        // obtenemos los descuentos para realizar las oportaciones
        $descuentos = Descuento::whereIn('type_descuento_id', $this->payload->pluck(['type_descuento_id']))
            ->whereIn('historial_id', $this->payload->pluck(['historial_id']))
            ->get();
        // actualizamos los descuentos
        foreach ($descuentos as $descuento) {
            // obtenemos la aportacion que pertenece al descuento
            $aportacion = $this->payload->where("type_descuento_id", $descuento->id)->first();
            // verificamos que la aportacion no sea null
            if ($aportacion) {
                // obtenemos el monto de la aportación
                $monto = $aportacion['monto'];
                // actualizmos la aportacion del descuento
                $descuento->update([ "monto" => $monto ]);
                continue;
            }
        }
    }

}
