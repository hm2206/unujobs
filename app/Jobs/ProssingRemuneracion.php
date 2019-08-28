<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use \DB;
use App\Collections\WorkCollection;
use App\Models\User;
use App\Notifications\BasicNotification;
use App\Models\TypeRemuneracion;
use App\Models\Remuneracion;

/**
 * Procesa las remuneraciones de los trabajadores
 */
class ProssingRemuneracion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    
    private $cronograma;
    private $jobs;
    private $types;
     

    public $timeout = 0;


    /**
     * @param \App\Models\Cronograma $cronograma
     * @param \App\Models\Work $jobs
     * @return void
     */
    public function __construct($cronograma, $jobs)
    {
        $this->cronograma = $cronograma;
        $this->jobs = $jobs;
        $this->types = TypeRemuneracion::where("activo", 1)->get();
    }

    /**
     * Se ejecuta automaticamente en las colas de trabajos
     *
     * @return void
     */
    public function handle()
    {
        $cronograma = $this->cronograma;
        
        $jobs = $this->jobs;

        foreach ($jobs as $job) {
            $this->configurarRemuneracion($cronograma, $job);
        }

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new BasicNotification("#", "Remuneraciones agregadas a los trabajadores"));
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
    private function configurarRemuneracion($cronograma, $job)
    {
        $mes = $cronograma->mes == 1 ? 12 : $cronograma->mes - 1;
        $year = $cronograma->mes == 1 ? $cronograma->año - 1 : $cronograma->año; 
        $infos = $job->infos->where("planilla_id", $cronograma->planilla_id);

        $isTypeBefore = Remuneracion::where("work_id", $job->id)
            ->where("mes", $mes)
            ->where("año", $year)
            ->where("adicional", $cronograma->adicional)
            ->whereIn("type_remuneracion_id", $this->types->pluck(['id']))
            ->get();
        // verificamos si hay un registro anterior
        if ($isTypeBefore->count()) {
            // recorremos los tipos de remuneraciones
            foreach ($this->types as $type) {
                // iteramos a cada informacion de la persona
                foreach ($infos as $info) {
                    // obtenemos un tipo de remuneracion especifica
                    $isType = $isTypeBefore->where("type_remuneracion_id", $type->id)
                        ->where("cargo_id", $info->cargo_id)
                        ->where("categoria_id", $info->categoria_id)
                        ->first();
                    // validamos si existe algúna remuneracion
                    if ($isType) {
                        $newRemuneracion = Remuneracion::updateOrCreate([
                            "work_id" => $job->id,
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

            $collection = new WorkCollection($job);
            $collection->createOrUpdateRemuneracion($cronograma);

        }
    }

}
