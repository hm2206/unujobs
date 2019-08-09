<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;

use App\Models\Meta;

class MetaImport implements ToCollection, WithHeadingRow
{

    use Importable;

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        
        foreach ($collection as $row) {

            $meta = Meta::where("metaID", $row['metaid'])->first();

            if (!$meta) {

                Meta::create([
                    "metaID" => $row['metaid'],
                    "meta" => $row['meta'],
                    "sectorID" => $row['sector'],
                    "sector" => $row['sector'],
                    "pliegoID" => $row['pliegoid'],
                    "pliego" => $row['pliego'],
                    "unidadID" => $row['unidadid'],
                    "unidad_ejecutora" => $row['unidad'],
                    "programaID" => $roe['programaid'],
                    "programa" => $row['programa'],
                    "funcionID" => $row['funcionid'],
                    "funcion" => $row['funcion'],
                    "subProgramaID" => $row['subProgramaid'],
                    "sub_programa" => $row['subPrograma'],
                    "actividadID" => $row['actividadid'],
                    "actividad" => $row['actividad']
                ]);
                
            }

        }

    }
}
