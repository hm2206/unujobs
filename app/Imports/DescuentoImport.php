<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;

use App\Models\Work;
use App\Models\Descuento;
use App\Models\TypeDescuento;
use App\Models\User;
use App\Models\Categoria;
use App\Notifications\BasicNotification;

/**
 * Modelo de importacion de descuentos en excel
 */
class DescuentoImport implements ToCollection, WithHeadingRow
{
    
    use Importable;

    private $cronograma;

    /**
     * @param \App\Models\Cronograma $cronograma
     * @param string $name
     */
    public function __construct($cronograma, $name)
    {
        $this->cronograma = $cronograma;
        $this->name = $name;
    }


    /**
     * Ejecuta el archivo de excel cuardando los datos correspondiente
     *
     * @param Collection $collection
     * @return void
     */
    public function collection(Collection $collection)
    {
        // obtenemos los tipo de descuentos
        $types = TypeDescuento::where("activo", 1)->get();

        foreach ($collection as $iter => $row) {

            // obtenemos a todos los trabajadores que pertenecen al cronograma
            $infos = $this->cronograma->infos;
            // buscar al trabajador por numero de documento
            $work = Work::where("numero_de_documento", $row['numero_de_documento'])->first();
            // verificar si el trabajador existe
            if ($work) {
                // obtenemos la informacion detallada del trabajador
                $info = $infos->where("work_id", $work->id)->first();
                
                if ($info) {

                    foreach ($types as $type) {
                        
                        // verificamos que el typeDescuento exísta
                        $isType = isset($row[$type->key]);

                        if ($isType) {
                            // obtenemos el monto del dsecuento
                            $monto = $row[$type->key];

                            // actualizamos o creamos el descuento del trabajador
                            $descuento = Descuento::where("info_id", $info->id)
                                ->where("type_descuento_id", $type->id)
                                ->first();
    
                            // verificamos que el valor a guardar sea un numero
                            if (\is_numeric($monto)) {

                                $descuento->update([
                                    "monto" => \round($monto, 2)
                                ]);

                            }
                        }
                        
                    }
                }

            }
        } 
    }

}
