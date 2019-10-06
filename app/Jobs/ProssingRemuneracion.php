<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use \DB;
use App\Collections\InfoCollection;
use App\Models\User;
use App\Notifications\BasicNotification;
use App\Models\TypeRemuneracion;
use App\Models\Remuneracion;
use App\Models\Info;

/**
 * Procesa las remuneraciones de los trabajadores
 */
class ProssingRemuneracion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    
    private $cronograma;
    private $infos;
    private $types;
     

    public $timeout = 0;


    /**
     * @param \App\Models\Cronograma $cronograma
     * @param \App\Models\Work $jobs
     * @return void
     */
    public function __construct($cronograma)
    {
        $this->cronograma = $cronograma;
    }

    /**
     * Se ejecuta automaticamente en las colas de trabajos
     *
     * @return void
     */
    public function handle()
    {
        // obtenemos los tipos de remuneracion
        $this->types = TypeRemuneracion::where("activo", 1)->get();
        // cronograma actual
        $cronograma = $this->cronograma;

        // infos associados al cronograma
        $this->infos = $cronograma->infos;

        // fecha anteriores
        $mes = $cronograma->mes == 1 ? 12 : $cronograma->mes - 1;
        $year = $cronograma->mes == 1 ? $cronograma->año - 1 : $cronograma->año; 
        // obtenemos remuneraciones anteriores
        $typeBefores = Remuneracion::whereIn("info_id", $this->infos->pluck(['id']))
            ->whereIn("type_remuneracion_id", $this->types->pluck(['id']))
            ->where("adicional", $cronograma->adicional)
            ->where("mes", $mes)
            ->where("año", $year)
            ->get();


        $infoOld = $this->infos->whereIn("id", $typeBefores->pluck('info_id'));
        $infoNew = $this->infos->whereNotIn("id", $infoOld->pluck('id'));

        if ($infoNew->count() > 0) {
            $this->configNewRemuneracion($cronograma, $infoNew);
        }

        if ($infoOld->count() > 0) {
            $this->configOldRemuneracion($cronograma, $infoOld, $typeBefores);
        }

    }


    private function configOldRemuneracion($cronograma, $infos, $befores) {

        $payload = [];

        foreach ($infos as $info) {

            foreach ($this->types as $type) {
                // obtenermos el registro anterior
                $typeBefore = $befores
                    ->where("type_remuneracion_id", $type->id)
                    ->where('info_id', $info->id)
                    ->first();
                // almacenamos el monto anterior
                $monto = isset($typeBefore->monto) ? $typeBefore->monto : 0;

                array_push($payload, [
                    "work_id" => $info->work_id,
                    "info_id" => $info->id,
                    "planilla_id" => $info->planilla_id,
                    "cargo_id" => $info->cargo_id,
                    "categoria_id" => $info->categoria_id,
                    "meta_id" => $info->meta_id,
                    "cronograma_id" => $cronograma->id,
                    "type_remuneracion_id" => $type->id,
                    "base" => $type->base,
                    "mes" => $cronograma->mes,
                    "año" => $cronograma->año,
                    "adicional" => $cronograma->adicional,
                    "monto" => $monto
                ]);

            }

        }

        foreach (array_chunk($payload, 1000) as $insert) {
            Remuneracion::insert($insert);
        }

    }
    

    private function configNewRemuneracion($cronograma, $infos) {
       foreach ($infos as $info) {
            $collection = new InfoCollection($info);
            $collection->createOrUpdateRemuneracion($cronograma, $this->types);
       }
    }


}
