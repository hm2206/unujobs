<?php

use Illuminate\Database\Seeder;
use App\Models\Info;
use App\Models\TypeAportacion;
use App\Models\Historial;
use App\Models\Aportacion;

class ConfigAportes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*$infos = Info::where('planilla_id', '<>', 4)->get();

        foreach ($infos as $info) {
            $info->type_aportaciones()->sync([1]);
        }*/

        $this->addAportacion();
        
    }


    public function addAportacion()
    {
        // obtener las aportaciones para procesar
        $this->types = TypeAportacion::with('type_descuento')->get();
         // obtenemos a los trabajadores de la planilla actual que tengan configuración de aportación
         $this->historial = Historial::whereHas('info', function($i){
            $i->whereHas('type_aportaciones');
        })->with(['info' => function($i) {
            $i->with('type_aportaciones');
        }])->get();
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
                $monto = $base >= $aportacion->minimo ? ($base * $aportacion->porcentaje) / 100 : $aportacion->minimo;
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
    }

}
