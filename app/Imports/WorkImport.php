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

            if ($work) {

                $ape_paterno = $row['ape_paterno'] ? $row['ape_paterno'] : $work->ape_paterno;
                $ape_materno = $row['ape_materno'] ? $row['ape_materno'] : $work->ape_materno;
                $nombres = $row['nombres'] ? $row['nombres'] : $work->nombres;
                $nombre_completo = $ape_paterno . " " . $ape_materno . " " . $nombres;

                $work->update([
                    "ape_paterno" => isset($row['ape_paterno']) ? $row['ape_paterno'] : $work->ape_paterno,
                    "ape_materno" => isset($row['ape_materno']) ? $row['ape_materno'] : $work->ape_materno,
                    "nombres" => isset($row['nombres']) ? $row['nombres'] : $work->nombres,
                    "nombre_completo" => $nombre_completo,
                    "direccion" => isset($row['direccion']) ? $row['direccion'] : $work->direccion,
                    "tipo_documento_id" => isset($row['tipo_documento_id']) ? $row['tipo_documento_id'] : $work->numero_de_documento,
                    "numero_de_documento" => isset($row['numero_de_documento']) ? $row['numero_de_documento'] : $work->numero_de_documento,
                    "fecha_de_nacimiento" => isset($row['fecha_de_nacimiento']) ? date($row['fecha_de_nacimiento']) : $work->fecha_de_nacimiento,
                    "profesion" => isset($row['profesion']) ? $row['profesion'] : $work->profesion,
                    "email" => isset($row['email']) ? $row['email'] : $work->email, 
                    "phone" => isset($row['phone']) ? $row['phone'] : $work->phone, 
                    "fecha_de_ingreso" => $fecha_de_ingreso ? date('Y-m-d', \strtotime($row['fecha_de_ingreso'])) : $work->fecha_de_ingreso,
                    "sexo" => isset($row['sexo']) ? (int)$row['sexo'] : $work->sexo,
                    "activo" => isset($row['activo']) ? (int)$row['activo'] : $work->activo
                ]);

            }else {

                $nombre_completo = $row['ape_paterno'] . " " . $row['ape_materno'] . " " . $row['nombres'];

                try {

                    Work::create([
                        "ape_paterno" => isset($row['ape_paterno']) ? $row['ape_paterno'] : '',
                        "ape_materno" => isset($row['ape_materno']) ? $row['ape_materno'] : '',
                        "nombres" => isset($row['nombres']) ? $row['nombres'] : 'worker',
                        "nombre_completo" => $nombre_completo,
                        "direccion" => isset($row['direccion']) ? $row['direccion'] : '',
                        "tipo_documento_id" => isset($row['tipo_documento_id']) ? $row['tipo_documento_id'] : 1,
                        "numero_de_documento" => isset($row['numero_de_documento']) ? $row['numero_de_documento'] : rand(11111111, 99999999),
                        "fecha_de_nacimiento" => isset($row['fecha_de_nacimiento']) ? date(strtotime($row['fecha_de_nacimiento'])) : null,
                        "profesion" => isset($row['profesion']) ? $row['profesion'] : 'Sr.',
                        "email" => isset($row['email']) ? $row['email'] : '', 
                        "phone" => isset($row['phone']) ? $row['phone'] : '',
                        "sexo" => isset($row['sexo']) ? (int)$row['sexo'] : 1,
                        "activo" => isset($row['activo']) ? (int)$row['activo'] : 1
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
