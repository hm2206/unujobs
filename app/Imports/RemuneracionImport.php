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
use App\Models\Info;

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
        $types = TypeRemuneracion::where("activo", 1)->get();
        $categorias = Categoria::all();
        $infos = $this->cronograma->infos;

        foreach ($collection as $row) {

            // obtenemos a todos los trabajadores que pertenecen al cronograma
            $cargo_id = isset($row["cargo"]) ? $row['cargo'] : '';
            $categoria_id = isset($row["categoria"]) ? $row['categoria'] : '';
            $categoria = $categorias->where("key", $categoria_id)->first();
            // buscar al trabajador por numero de documento
            $infos = $this->cronograma->infos;
            // buscar al trabajador por numero de documento
            $work = Work::where("numero_de_documento", $row['numero_de_documento'])->first();
            
            // verificar si el trabajador existe
            if ($work) {
                // obtenemos la categoria
                $info = Info::where("work_id", $work->id)
                    ->where("cargo_id", $cargo_id)
                    ->where("categoria_id", isset($categoria->id) ? $categoria->id : '')
                    ->first();
                
                if ($info) {


                    foreach ($types as $type) {
                        
                         // verificamos que el typeRemuneracion exísta
                        $isType = isset($row[$type->key]);
                        if ($isType) {

                            // obtenemos el monto de la remuneración
                            $monto = $row[$type->key];

                            // obtener las remuneraciones
                            $remuneracion = Remuneracion::where("info_id", $info->id)
                                ->where("cronograma_id", $this->cronograma->id)
                                ->where("type_remuneracion_id", $type->id)
                                ->first();
                            
                            // verificamos que el valor a guardar sea un numero
                            if (\is_numeric($monto)) {

                                $remuneracion->update([
                                    "monto" => \round($monto, 2)
                                ]);

                            }

                        }
                    }
                   
                }else {
                    \Log::info("persona => {$work->numero_de_documento}, cargo => {$cargo_id}, categoria => $categoria_id");
                }
            }else {
                \Log::info("persona => {$row['numero_de_documento']}");
            }
        }  
    }

}
