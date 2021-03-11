<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;

use App\Models\Categoria;

class CategoriaImport implements ToCollection, WithHeadingRow
{

    use Importable;

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        
        foreach ($collection as $row) {
            
            $categoria = Categoria::where("key", $row['key'])->first();

            if ($categoria) {
                
                $categoria->update([
                    "nombre" => $row['descripcion']
                ]);

            }else {

                Categoria::create([
                    "key" => $row['key'],
                    "nombre" => $row['descripcion']
                ]);

            }

        }

    }
}
