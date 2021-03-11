<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;

use App\Models\Categoria;
use App\Models\Concepto;

class CategoriaConceptoImport implements ToCollection, WithHeadingRow
{
    use Importable;
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        
        foreach ($collection as $row) {
           
            $categoria = Categoria::where('key', $row['categoria'])->first();
            $concepto = Concepto::where('key', $row['concepto'])->first();

            if ($categoria && $concepto) {

                $monto = (double)$row['monto'];
                $categoria->conceptos()->syncWithoutDetaching($concepto->id);
                $categoria->conceptos()->updateExistingPivot($concepto->id, ["monto" => $monto]);

            }

        }

    }
}
