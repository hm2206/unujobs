<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;

use App\Models\Work;
use App\Models\Info;
use App\Models\Categoria;
use App\Models\Planilla;
use App\Models\Meta;

class WorkConfigImport implements ToCollection, WithHeadingRow
{

    use Importable;


    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        
        foreach ($collection as $row) {
            
            $numero_de_documento = $row['numero_de_documento'];

            $work = Work::where("numero_de_documento", $numero_de_documento)->first();
            $categoria = Categoria::where("key", $row['categoria'])->first();
            $planilla = Planilla::where("key", $row['planilla'])->first();
            $meta = Meta::where('metaID', $row['meta'])->first();

            if ($work && $categoria && $meta) {

                $info = Info::updateOrCreate([
                    "work_id" => $work->id,
                    "cargo_id" => $row['cargo'],
                    "categoria_id" => $categoria->id,
                    "planilla_id" => $row['planilla'],
                    "meta_id" => $meta->id
                ]);

                $info->update([
                    "perfil" => $row['perfil'],
                    "plaza" => $row['plaza'],
                    "escuela" => $row['escuela'],
                    "observacion" => $row['observacion'],
                    "fuente_id" => isset($row['fuente']) ? (int)$row['fuente'] : null,
                    "ruc" => $row['ruc']
                ]);

            }else {

                \Log::info([
                    "work" => $work,
                    "categoria" => $categoria,
                    "planilla" => $planilla,
                    "meta" => $meta
                ]);

            }

        }

    }
}
