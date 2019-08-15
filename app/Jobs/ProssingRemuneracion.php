<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use \DB;
use App\Models\Work;
use App\Models\Cronograma;
use App\Models\TypeRemuneracion;
use App\Models\Remuneracion;
use App\Models\User;
use App\Notifications\BasicNotification;

/**
 * Procesa las remuneraciones de los trabajadores
 */
class ProssingRemuneracion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    
    private $cronograma;
    private $jobs;
     
    /**
     * @param \App\Models\Cronograma $cronograma
     * @param \App\Models\Work $jobs
     * @return void
     */
    public function __construct($cronograma, $jobs)
    {
        $this->cronograma = $cronograma;
        $this->jobs = $jobs;
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

        $cronograma->works()->syncWithoutDetaching($jobs->pluck(["id"]));

        $types = TypeRemuneracion::all();

        foreach ($jobs as $job) {
            $this->configurarRemuneracion($types, $cronograma, $job);
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
    private function configurarRemuneracion($types, $cronograma, $job)
    {
        $total = 0;
        $mes = $cronograma->mes == 1 ? 12 : $cronograma->mes - 1;
        $year = $cronograma->mes == 1 ? $cronograma->año - 1 : $cronograma->año;
       
        try {

            foreach ($job->infos as $info) {
    
                $current_total = 0;
    
                $hasRemuneraciones = Remuneracion::where("work_id", $job->id)
                    ->where("cargo_id", $info->cargo_id)
                    ->where("categoria_id", $info->categoria_id)
                    ->where("planilla_id", $info->planilla_id)
                    ->where("adicional", $cronograma->adicional)
                    ->where("dias", $cronograma->dias)
                    ->where("mes", $mes)
                    ->where("año", $year)
                    ->get();
    
                
                if ($hasRemuneraciones->count() > 0) {
                    foreach ($hasRemuneraciones as $remuneracion) {
    
                        $current_monto = $job->descanso ? 0 : round($remuneracion->monto, 2);
    
                        Remuneracion::updateOrCreate([
                            "work_id" => $job->id,
                            "categoria_id" => $info->categoria_id,
                            "cronograma_id" => $cronograma->id,
                            "cargo_id" => $info->cargo_id,
                            "planilla_id" => $info->planilla_id,
                            "type_remuneracion_id" => $remuneracion->id,
                            "mes" => $cronograma->mes,
                            "año" => $cronograma->año,
                            "monto" => $current_monto,
                            "adicional" => $cronograma->adicional,
                            "dias" => $cronograma->dias,
                            "base" => $remuneracion->base,
                            "meta_id" => $remuneracion->meta_id
                        ]);
    
                        $current_total += $current_monto;
                    }
                }else {
                    foreach ($types as $type) {
                        $config = DB::table("concepto_type_remuneracion")
                            ->whereIn("concepto_id", $info->categoria->conceptos->pluck(["id"]))
                            ->where("categoria_id", $info->categoria_id)
                            ->where("type_remuneracion_id", $type->id)
                            ->get();
    
                        $suma = $config->sum("monto");
    
                        $current_monto = $job->descanso ? 0 : \round(($suma * $cronograma->dias) / 30, 2);
    
                        Remuneracion::updateOrCreate([
                            "work_id" => $job->id,
                            "categoria_id" => $info->categoria_id,
                            "cargo_id" => $info->cargo_id,
                            "planilla_id" => $info->planilla_id,
                            "cronograma_id" => $cronograma->id,
                            "type_remuneracion_id" => $type->id,
                            "mes" => $cronograma->mes,
                            "año" => $cronograma->año,
                            "monto" => $current_monto,
                            "adicional" => $cronograma->adicional,
                            "base" => $type->base,
                            "meta_id" => $info->meta_id
                        ]);
    
                        $current_total += $current_monto;
                    }
                }
    
                $info->update(["total" => $current_total]);
    
                $total += $current_total;
    
            }

        } catch (\Throwable $th) {
            
            \Log::info($th);

        }


    }

}
