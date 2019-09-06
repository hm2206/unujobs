<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

use App\Models\Work;
use App\Models\User;
use App\Notifications\BasicNotification;
use \Carbon\Carbon;

/**
 * Modelo de importación de los Trabajadores
 */
class WorkImport implements ToCollection, WithHeadingRow
{

    use Importable;

    /**
     * Ejecuta el archivo de excel y almacena los datos de los nuevos trabajadores
     *
     * @param Collection $collection
     * @return void
     */
    public function collection(Collection $collection)
    {

        foreach ($collection as $row) {

            $work = Work::where("numero_de_documento", $row['numero_de_documento'])->first();
            $fecha_de_ingreso = isset($row['fecha_de_ingreso']);

            if (!$work) {

                $ape_paterno = $row['ape_paterno'] ? $row['ape_paterno'] : $work->ape_paterno;
                $ape_materno = $row['ape_materno'] ? $row['ape_materno'] : $work->ape_materno;
                $nombres = $row['nombres'] ? $row['nombres'] : $work->nombres;
                $nombre_completo = $ape_paterno . " " . $ape_paterno . " " . $nombres;

                $work->update([
                    "ape_paterno" => isset($row['ape_paterno']) ? $row['ape_paterno'] : $work->ape_paterno,
                    "ape_materno" => isset($row['ape_materno']) ? $row['ape_materno'] : $work->ape_materno,
                    "nombres" => isset($row['nombres']) ? $row['nombres'] : $work->nombres,
                    "nombre_completo" => $nombre_completo,
                    "direccion" => isset($row['direccion']) ? $row['direccion'] : $work->direccion,
                    "tipo_documento_id" => isset($row['tipo_documento_id']) ? $row['tipo_documento_id'] : $work->numero_de_documento,
                    "numero_de_documento" => isset($row['numero_de_documento']) ? $row['numero_de_documento'] : $work->numero_de_documento,
                    "fecha_de_nacimiento" => isset($row['fecha_de_nacimiento']) ? date(strtotime($row['fecha_de_nacimiento'])) : $work->fecha_de_nacimiento,
                    "profesion" => isset($row['profesion']) ? $row['profesion'] : $work->profesion,
                    "phone" => isset($row['phone']) ? $row['phone'] : $work->phone, 
                    "fecha_de_ingreso" => $fecha_de_ingreso ? date('Y-m-d', \strtotime($row['fecha_de_ingreso'])) : $work->fecha_de_ingreso,
                    "sexo" => isset($row['sexo']) ? (int)$row['sexo'] : $work->sexo,
                    "numero_de_essalud" => isset($row['numero_de_essalud']) ? $row['numero_de_essalud'] : $work->numero_de_essalud,
                    "banco_id" => isset($row['banco_id']) ? (int)$row['banco_id'] : $work->banco_id,
                    "numero_de_cuenta" => isset($row['numero_de_cuenta']) ? $row['numero_de_cuenta'] : $work->numero_de_cuenta,
                    "afp_id" => isset($row['afp_id']) ? (int)$row['afp_id'] : $work->afp_id,
                    "type_afp" => isset($row['type_afp']) ? $row['type_afp'] : $work->type_afp,
                    "fecha_de_afiliacion" => isset($row['fecha_de_afiliacion']) ? date('Y-m-d', \strtotime($row['fecha_de_afiliacion'])) : $work->fecha_de_afiliacion,
                    "plaza" => isset($row['plaza']) ? $row['plaza'] : $work->plaza,
                    "numero_de_cussp" => isset($row['numero_de_cussp']) ? $row['numero_de_cussp'] : $work->numero_de_cussp,
                    "accidentes" => isset($row['accidentes']) ? (int)$row['accidentes'] : $work->accidentes,
                    "sede_id" => isset($row['sede_id']) ? (int)$row['sede_id'] : $work->sede_id,
                    "ley" => isset($row['ley']) ? $row['ley'] : $work->ley,
                ]);

            }else {

                $nombre_completo = $row['ape_paterno'] . " " . $row['ape_materno'] . " " . $row['nombres'];

                try {

                    Work::create([
                        "ape_paterno" => isset($row['ape_paterno']) ? $row['ape_paterno'] : 'worker',
                        "ape_materno" => isset($row['ape_materno']) ? $row['ape_materno'] : 'worker',
                        "nombres" => isset($row['nombres']) ? $row['nombres'] : 'worker',
                        "nombre_completo" => $nombre_completo,
                        "direccion" => isset($row['direccion']) ? $row['direccion'] : '',
                        "tipo_documento_id" => isset($row['tipo_documento_id']) ? $row['tipo_documento_id'] : 1,
                        "numero_de_documento" => isset($row['numero_de_documento']) ? $row['numero_de_documento'] : rand(11111111, 99999999),
                        "fecha_de_nacimiento" => isset($row['fecha_de_nacimiento']) ? date(strtotime($row['fecha_de_nacimiento'])) : null,
                        "profesion" => isset($row['profesion']) ? $row['profesion'] : 'indefinido',
                        "phone" => isset($row['phone']) ? $row['phone'] : '', 
                        "fecha_de_ingreso" => $fecha_de_ingreso ? date('Y-m-d', \strtotime($row['fecha_de_ingreso'])) : date('y-m-d'),
                        "sexo" => isset($row['sexo']) ? (int)$row['sexo'] : 1,
                        "numero_de_essalud" => isset($row['numero_de_essalud']) ? $row['numero_de_essalud'] : null,
                        "banco_id" => isset($row['banco_id']) ? (int)$row['banco_id'] : null,
                        "numero_de_cuenta" => isset($row['numero_de_cuenta']) ? $row['numero_de_cuenta'] : null,
                        "afp_id" => isset($row['afp_id']) ? (int)$row['afp_id'] : null,
                        "type_afp" => isset($row['type_afp']) ? $row['type_afp'] : null,
                        "fecha_de_afiliacion" => isset($row['fecha_de_afiliacion']) ? date('Y-m-d', \strtotime($row['fecha_de_afiliacion'])) : null,
                        "plaza" => isset($row['plaza']) ? $row['plaza'] : null,
                        "numero_de_cussp" => isset($row['numero_de_cussp']) ? $row['numero_de_cussp'] : null,
                        "accidentes" => isset($row['accidentes']) ? (int)$row['accidentes'] : 0,
                        "sede_id" => isset($row['sede_id']) ? (int)$row['sede_id'] : 1,
                        "ley" => isset($row['ley']) ? $row['ley'] : null,
                    ]);

                } catch (\Throwable $th) {
                    
                    \Log::info($th);

                }
            }
        }

    }

    /**
     * Indica la cabecera esa en la primera fila del archivo de excel
     *
     * @return integer
     */
    public function headingRow(): int
    {
        return 1;
    }


}
