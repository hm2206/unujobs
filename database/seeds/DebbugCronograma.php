<?php

use Illuminate\Database\Seeder;
use App\Models\Cronograma;
use App\Models\Historial;
use App\Models\TypeRemuneracion;
use App\Models\Remuneracion;
use App\Models\TypeDescuento;
use App\Models\Descuento;
use App\Models\Info;

class DebbugCronograma extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->ordenarHistorial();
        // $this->eliminarHistorialDuplicado();
        // $this->copiarCronograma();
        // $this->orderDescuentos();
        $this->addFechaIngreso();
    }


    public function recrearRemuneraciones() {

        $boletas = Boleta::get();
        $payload = [];

        $types = TypeRemuneracion::whereHas('type_remuneracion')->get();

        \Log::info($types);

        foreach ($boletas as $boleta) {
            foreach ($types as $type) {
                array_push($payload, [
                    "work_id" => $boleta->work_id,
                    "categoria_id" => $boleta->categoria_id,
                    "cargo_id" => $boleta->cargo_id,
                    "dias" => "30",
                    "cronograma_id" => $boleta->cronograma_id,
                    "observaciones" => "",
                    "sede_id" => '1',
                    "type_remuneracion_id" => $type->id,
                    "mes" => '8',
                    'año' => '2019',
                    'monto' => 0,
                    'adicional' => 0,
                    'horas' => '8',
                    'base' => $type->base,
                    'planilla_id' => $type->planilla_id,
                    'meta_id' => $boleta->meta_id,
                    'info_id' => $boleta->info_id,
                    'show' => $type->show
                ]);
            }
        }

        Remuneracion::insert($payload);

    }

    public function ordenarHistorial()
    {
        $historial = Historial::with('work')->get();
        foreach ($historial as $history) {
            $work = $history->work;
            $history->update(["orden" => $work ? substr($work->nombre_completo, 0, 3) : '']);
        }   
    }


    public function eliminarHistorialDuplicado()
    {
        $cronograma = Cronograma::with('historial')->findOrFail(35);
        foreach ($cronograma->historial as $history) {
            foreach ($cronograma->historial->where('historial_id', '<>', $history->id) as $other) {
                if ($history->info_id == $other->info_id) {
                    // eliminar datos dependiente
                    $other->descuentos()->delete();
                    $other->remuneraciones()->delete();
                    $other->aportaciones()->delete();
                    $other->delete();
                    break;
                }
            }
        }
    }

    public function copiarCronograma()
    {
        $cronograma = Cronograma::where("año", "2019")
            ->where("mes", "10")
            ->where('planilla_id', 4)
            ->firstOrFail();
        // historial a copiar
        $cronogramaActual = Cronograma::with('historial')
            ->where("año", "2019")
            ->where("mes", "11")
            ->where('planilla_id', 4)
            ->firstOrFail();
        //  remuneraciones
        $remuneraciones = Remuneracion::where('cronograma_id', $cronograma->id)->get();
        foreach ($cronogramaActual->historial as $history) {
            foreach ($history->remuneraciones as $rem) {
                $newType = $remuneraciones->where('type_remuneracion_id', $rem->type_remuneracion_id)
                    ->where('work_id', $history->work_id)
                    ->first();
                if ($newType) {
                    // actualizar remuneración
                    $rem->update(["monto" => $newType->monto ]);
                }
            }
        }
    }


    public function orderDescuentos() 
    {
        $types = TypeDescuento::all();
        foreach ($types as $type) {
            Descuento::where('type_descuento_id', $type->id)->update([ "orden" => $type->orden ]);
        }
    }


    public function orderRemuneraciones()
    {
        $types = TypeRemuneracion::all();
        foreach ($types as $type) {
            Remuneracion::where('type_remuneracion_id', $type->id)->update([ "orden" => $type->orden ]);
        }
    }


    public function addFechaIngreso()
    {
        $infos = Info::where("fecha_de_ingreso", "<>", "")->get();
        $historial = Historial::whereIn("info_id", $infos->pluck(['id']))->get();
        foreach ($infos as $info) {
           $histories = $historial->where("info_id", $info->id);
           foreach ($histories as $history) {
               $history->update(["fecha_de_ingreso" => $info->fecha_de_ingreso]);
           }
        }
    }

}
