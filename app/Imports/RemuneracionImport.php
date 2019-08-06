<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;

use App\Models\Work;
use App\Models\Remuneracion;
use App\Models\TypeRemuneracion;
use App\Jobs\ImportQueue;
use App\Models\User;
use App\Notifications\BasicNotification;

/**
 * modelo de importación de remuneraciones de los trabajadores
 */
class RemuneracionImport implements ToCollection, WithHeadingRow
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
     * Ejecuta el archivo excel guardando los datos de las remuneraciones.
     *
     * @param Collection $collection
     * @return void
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {

            // obtenemos a todos los trabajadores que pertenecen al cronograma
            $workIn = Remuneracion::where("cronograma_id", $this->cronograma->id)
                ->get()->pluck(["work_id"]);
            $works = Work::whereIn("id", $workIn)->get();
            // buscar al trabajador por numero de documento
            $work = $works->where("numero_de_documento", $row['numero_de_documento'])->first();
            // verificar si el trabajador existe
            if ($work) {
                // obtenemos la informacion detallada del trabajador
                foreach ($work->infos as $info) {
                    // obtenemos el tipo de remuneracion
                    $type = TypeRemuneracion::where("key", $row['remuneracion'])->first();
                    // verificamos que el typeRemuneracion exista
                    if ($type) {
                        // obtenemos las remuneraciones del trabajador
                        $remuneracion = Remuneracion::updateOrCreate([
                            "work_id" => $work->id,
                            "categoria_id" => $info->categoria_id,
                            "cargo_id" => $info->cargo_id,
                            "planilla_id" => $info->planilla_id,
                            "cronograma_id" => $this->cronograma->id,
                            "type_remuneracion_id" => $type->id,
                            "adicional" => $this->cronograma->adicional,
                            "base" => $type->base
                        ]);
    
                        $remuneracion->update([
                            "monto" => isset($row['monto']) ? $row['monto'] : 0
                        ]);
                    }

                    $total = Remuneracion::where("work_id", $work->id)
                        ->where("categoria_id", $info->categoria_id)
                        ->where("cargo_id", $info->cargo_id)
                        ->where("planilla_id", $info->planilla_id)
                        ->where("cronograma_id", $this->cronograma->id)
                        ->get()->sum("monto");

                    $info->update([
                        "total" => $total
                    ]);
                }
            }
        }  
    }

}
