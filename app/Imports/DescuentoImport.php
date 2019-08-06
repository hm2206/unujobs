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
        foreach ($collection as $iter => $row) {

            // obtenemos a todos los trabajadores que pertenecen al cronograma
            $workIn = Descuento::where("cronograma_id", $this->cronograma->id)
                ->get()->pluck(["work_id"]);
            $works = Work::whereIn("id", $workIn)->get();
            // buscar al trabajador por numero de documento
            $work = $works->where("numero_de_documento", $row['numero_de_documento'])->first();
            // verificar si el trabajador existe
            if ($work) {
                // obtenemos la informacion detallada del trabajador
                foreach ($work->infos as $info) {
                    // obtenemos el tipo de remuneracion
                    $type = TypeDescuento::where("key", $row['descuento'])->first();
                    // verificamos que el typeRemuneracion exista
                    if ($type) {
                        // obtenemos las remuneraciones del trabajador
                        $descuento = Descuento::updateOrCreate([
                            "work_id" => $work->id,
                            "categoria_id" => $info->categoria_id,
                            "cargo_id" => $info->cargo_id,
                            "planilla_id" => $info->planilla_id,
                            "cronograma_id" => $this->cronograma->id,
                            "type_descuento_id" => $type->id,
                            "adicional" => $this->cronograma->adicional,
                        ]);
    
                        $descuento->update([
                            "monto" => isset($row['monto']) ? $row['monto'] : 0
                        ]);
                    }
                }
            }
        } 
    }

}
