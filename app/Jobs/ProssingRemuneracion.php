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
    private $infoIn;
    private $types;
     

    public $timeout = 0;


    /**
     * @param \App\Models\Cronograma $cronograma
     * @param \App\Models\Work $jobs
     * @return void
     */
    public function __construct($cronograma, $infoIn)
    {
        $this->cronograma = $cronograma;
        $this->infoIn = $infoIn;
        $this->infos = Info::with("work")->whereIn("id", $infoIn)->get();
        $this->types = TypeRemuneracion::where("activo", 1)->get();
    }

    /**
     * Se ejecuta automaticamente en las colas de trabajos
     *
     * @return void
     */
    public function handle()
    {
        // cronograma actual
        $cronograma = $this->cronograma;
        // fecha anteriores
        $mes = $cronograma->mes == 1 ? 12 : $cronograma->mes - 1;
        $year = $cronograma->mes == 1 ? $cronograma->año - 1 : $cronograma->año; 
        // obtenemos remuneraciones anteriores
        $typeBefores = Remuneracion::whereIn("info_id", $this->infoIn)
            ->whereIn("type_remuneracion_id", $this->types->pluck(['id']))
            ->where("adicional", $cronograma->adicional)
            ->where("mes", $mes)
            ->where("año", $year)
            ->get();

        foreach ($this->infos as $info) {
            $this->configurarRemuneracion($cronograma, $info, $typeBefores);
        }

    }

    /**
     * Se encarga de configurar y procesar las remuneraciones de cada trabajador
     *
     * @param \App\Models\TypeRemuneracion $types
     * @param \App\Models\Cronograma $cronograma
     * @param \App\Models\Work $job
     * @return void
     */
    private function configurarRemuneracion($cronograma, $info, $typeBefores)
    {
        // obtener remuneracion de un mes anterior
        $isTypeBefore = $typeBefores->where("info_id", $info->id);

        // verificamos si hay un registro anterior
        if ($isTypeBefore->count()) {
            // recorremos los tipos de remuneraciones
            foreach ($this->types as $type) {
                // iteramos a cada informacion de la persona
                foreach ($infos as $info) {
                    // obtenemos un tipo de remuneracion especifica
                    $isType = $isTypeBefore->where("type_remuneracion_id", $type->id)->first();
                    // validamos si existe algúna remuneracion
                    if ($isType) {
                        $newRemuneracion = Remuneracion::updateOrCreate([
                            "work_id" => $info->work_id,
                            "info_id" => $info->id,
                            "planilla_id" => $isType->planilla_id,
                            "cargo_id" => $isType->cargo_id,
                            "categoria_id" => $isType->categoria_id,
                            "meta_id" => $isType->meta_id,
                            "cronograma_id" => $cronograma->id,
                            "type_remuneracion_id" => $type->id,
                            "base" => $type->base,
                            "mes" => $cronograma->mes,
                            "año" => $cronograma->año,
                            "adicional" => $cronograma->adicional
                        ]);
                        // actualizamos un monto
                        $newRemuneracion->update(["monto" => $isType->monto]);
                    }
                }
            }
        }else {

            $collection = new InfoCollection($info);
            $collection->createOrUpdateRemuneracion($cronograma, $this->types);

        }
    }

}
