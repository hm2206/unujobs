<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use App\Jobs\InfoDisabled;

use App\Models\Work;
use App\Models\Info;
use App\Models\Categoria;
use App\Models\Planilla;
use App\Models\Meta;

class WorkConfigImport implements ToCollection, WithHeadingRow
{

    use Importable;

    private $infos;
    private $disabled = 1;

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {

        $payload = [];
        $errores = [];
        $this->infos = \collect();
        
        foreach ($collection as $row) {
            // obtener numero de documento del trabajador a importar
            $numero_de_documento = $row['numero_de_documento'];
            // obtener trabajador por numero de documento
            $work = Work::where("numero_de_documento", $numero_de_documento)->first();
            // obtener categoria
            $categoria = Categoria::where("key", $row['categoria'])->first();
            // obtener meta presupuestal
            $meta = Meta::where('metaID', $row['meta'])->first();
            // verificar que el usuario exista
            if ($work && $categoria && $meta) {
                // verificar que el info exista
                $info = Info::where("planilla_id", $row['planilla'])
                    ->where("cargo_id", $row['cargo'])
                    ->where("categoria_id", $categoria->id)
                    ->where("meta_id", $meta->id)
                    ->where('work_id', $work->id)
                    ->first();

                if ($info) {
                    $active = isset($row['active']) && $row['active'] == 1 ? 1 : 0;
                    // actualizar los datos del info
                    $info->update([
                        "sindicato_id" => isset($row['sindicato']) ? $row['sindicato'] : $info->sindicato_id,
                        "afp_id" => isset($row['afp']) ? $row['afp'] : $info->afp_id,
                        "type_afp_id" => isset($row['type_afp']) ? $row['type_afp'] : $info->type_afp_id,
                        "numero_de_cussp" => isset($row['numero_de_cussp']) ? $row['numero_de_cussp'] : $info->numero_de_cussp,
                        "fecha_de_afiliacion" => isset($row['fecha_de_afiliacion']) ? $row['fecha_de_afiliacion'] : $info->fecha_de_afiliacion,
                        "banco_id" => isset($row['banco']) ? $row['banco'] : $info->banco_id,
                        "numero_de_cuenta" => isset($row['numero_de_cuenta']) ? $row['numero_de_cuenta'] : $info->numero_de_cuenta,
                        "numero_de_essalud" => isset($row['numero_de_essalud']) ? $row['numero_de_essalud'] : $info->numeo_de_essalud,
                        "fuente_id" => isset($row['fuente_id']) ? $row['fuente_id'] : $info->fuente_id,
                        "plaza" => isset($row['plaza']) ? $row['plaza'] : $info->plaza,
                        "perfil" => isset($row['perfil']) ? $row['perfil'] : $info->plaza,
                        "escuela" => isset($row['escuela']) ? $row['escuela'] : $info->escuela,
                        "ruc" => isset($row['ruc']) ? $row['ruc'] : $info->ruc,
                        "pap" => isset($row['pap']) ? $row['pap'] : $info->pap,
                        "fecha_de_ingreso" => isset($row['fecha_de_ingreso']) ? $row['fecha_de_ingreso'] : $info->fecha_de_ingreso,
                        "fecha_de_cese" => isset($row['fecha_de_cese']) ? $row['fecha_de_cese'] : $info->fecha_de_cese,
                        "afecto" => isset($row['afecto']) ? $row['afecto'] : $info->afecto,
                        "active" => isset($row['active']) ? $row['active'] : 0
                    ]); 
                    
                    // modo disabled
                    if ($this->disabled) {
                        $this->infos->push($info);
                    }

                }else {
                    // almacenar los nuevos registros para una inserción masiva
                    array_push($payload, [
                        "work_id" => $work->id,
                        "planilla_id" => $row['planilla'],
                        "cargo_id" => $row['cargo'],
                        "categoria_id" => $categoria->id,
                        "meta_id" => $meta->id,
                        "sindicato_id" => isset($row['sindicato']) ? $row['sindicato'] : null,
                        "afp_id" => isset($row['afp']) ? $row['afp'] : null,
                        "type_afp_id" => isset($row['type_afp']) ? $row['type_afp'] : null,
                        "numero_de_cussp" => isset($row['numero_de_cussp']) ? $row['numero_de_cussp'] : null,
                        "fecha_de_afiliacion" => isset($row['fecha_de_afiliacion']) ? $row['fecha_de_afiliacion'] : null,
                        "banco_id" => isset($row['banco']) ? $row['banco'] : null,
                        "numero_de_cuenta" => isset($row['numero_de_cuenta']) ? $row['numero_de_cuenta'] : null,
                        "numero_de_essalud" => isset($row['numero_de_essalud']) ? $row['numero_de_essalud'] : null,
                        "fuente_id" => isset($row['fuente_id']) ? $row['fuente_id'] : null,
                        "plaza" => isset($row['plaza']) ? $row['plaza'] : null,
                        "perfil" => isset($row['perfil']) ? $row['perfil'] : null,
                        "escuela" => isset($row['escuela']) ? $row['escuela'] : null,
                        "ruc" => isset($row['ruc']) ? $row['ruc'] : null,
                        "pap" => isset($row['pap']) ? $row['pap'] : null,
                        "fecha_de_ingreso" => isset($row['fecha_de_ingreso']) ? $row['fecha_de_ingreso'] : date('Y-m-d'),
                        "fecha_de_cese" => isset($row['fecha_de_cese']) ? $row['fecha_de_cese'] : null,
                        "afecto" => isset($row['afecto']) ? $row['afecto'] : 1,
                        "active" => isset($row['active']) ? $row['active'] : 1
                    ]);
                    // modo disabled
                    if ($this->disabled) {
                        $this->infos->push($payload);
                    }
                }

            }else {
                // almacenar los errores
                array_push($errores, [
                    "error" => "El trabajador no existe",
                    "payload" => json_encode($row)
                ]);

            }

        }


        // agregar los nuevos infos masivamente
        foreach (array_chunk($payload, 1000) as $insert) {
            Info::insert($payload);
        }

        // verificar si hay errores e imprimir en el log
        if (count($errores) > 0) {
            \Log::info($errores);
        }
    }
}
