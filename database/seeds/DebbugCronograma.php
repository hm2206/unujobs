<?php

use Illuminate\Database\Seeder;
use App\Models\Cronograma;
use App\Models\Boleta;

class DebbugCronograma extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
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
}
