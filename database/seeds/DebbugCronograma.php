<?php

use Illuminate\Database\Seeder;
use App\Models\Cronograma;
use App\Models\Historial;
use App\Models\TypeRemuneracion;
use App\Models\Remuneracion;

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
        $this->eliminarHistorialDuplicado();
    }


    public function boletas () {
        $cronogramas = Cronograma::where("mes", '8')->get();

        foreach ($cronogramas as $cronograma) {
            $infos = $cronograma->infos;
            foreach ($infos as $info) {
                Boleta::where("cronograma_id", $cronograma->id)
                    ->where("info_id", $info->id)
                    ->update([
                        "observacion" => $info->observacion,
                        "meta_id" => $info->meta_id,
                        "pap" => $info->pap,
                        "ext_pptto" => $info->cargo->ext_pptto,
                        "afp_id" => $info->work->afp_id,
                        "perfil" => $info->perfil,
                        "planilla_id" => $info->planilla_id,
                        "cargo_id" => $info->cargo_id,
                        "categoria_id" => $info->categoria_id,
                        "numero_de_cussp" => $info->work->numero_de_cussp,
                        "numero_de_essalud" => $info->work->numero_de_essalud,
                        "meta_id" => $info->meta_id,
                        "work_id" => $info->work_id,
                        "plaza" => $info->plaza,
                        "escuela" => $info->escuela,
                        "sindicato_id" => $info->sindicato_id
                    ]);
            }
        }
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

}
