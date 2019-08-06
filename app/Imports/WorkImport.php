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

            if (!$work) {
                Work::create([
                    "ape_paterno" => isset($row['ape_paterno']) ? $row['ape_paterno'] : 'worker',
                    "ape_materno" => isset($row['ape_materno']) ? $row['ape_materno'] : 'worker',
                    "nombres" => isset($row['nombres']) ? $row['nombres'] : 'worker',
                    "nombre_completo" => isset($row['nombre_completo']) ? $row['nombre_completo'] : 'worker',
                    "direccion" => isset($row['direccion']) ? $row['direccion'] : '',
                    "tipo_documento_id" => isset($row['tipo_documento_id']) ? $row['tipo_documento_id'] : 1,
                    "numero_de_documento" => isset($row['numero_de_documento']) ? $row['numero_de_documento'] : rand(11111111, 99999999),
                    "fecha_de_nacimiento" => isset($row['fecha_de_nacimiento']) ? date('Y-m-d', \strtotime($row['fecha_de_nacimiento'])) : null,
                    "profesion" => isset($row['profesion']) ? $row['profesion'] : 'indefinido',
                    "phone" => isset($row['phone']) ? $row['phone'] : '',
                    "fecha_de_ingreso" => isset($row['fecha_de_ingreso']) ? date('Y-m-d', strtotime($row['fecha_de_ingreso'])) : date('Y.m-d'),
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
