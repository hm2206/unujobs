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
use App\Models\Categoria;
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

        // obtenemos los tipo de remuneracion
        $types = TypeRemuneracion::all();

        foreach ($collection as $row) {

            // obtenemos a todos los trabajadores que pertenecen al cronograma
            $works = $this->cronograma->works;
            // buscar al trabajador por numero de documento
            $work = $works->where("numero_de_documento", $row['numero_de_documento'])->first();
            
            // verificar si el trabajador existe
            if ($work) {
                // obtenemos la categoria
                $categoria = Categoria::where("key", $row['categoria'])->first();
                // obtenemos la informacion detallada del trabajador
                $info = $work->infos->where("categoria_id", $categoria->id)->first();
                
                if ($info) {


                    foreach ($types as $type) {
                        
                         // verificamos que el typeRemuneracion exísta
                        $isType = isset($row[$type->key]);
                        if ($isType) {

                            // obtenemos el monto de la remuneración
                            $monto = $row[$type->key];
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
                            
                            // verificamos que el valor a guardar sea un numero
                            if (\is_numeric($monto)) {

                                $remuneracion->update([
                                    "monto" => \round($monto, 2)
                                ]);

                            }

                        }
                    }

                    // actualizamos el monto actual
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
