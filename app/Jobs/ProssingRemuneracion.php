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

class ProssingRemuneracion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    
    private $cronograma;
    private $jobs;
     

    public function __construct($cronograma, $jobs)
    {
        $this->cronograma = $cronograma;
        $this->jobs = $jobs;
    }


    public function handle()
    {
        $cronograma = $this->cronograma;
        $jobs = $this->jobs;
        $types = TypeRemuneracion::all();

        foreach ($jobs as $job) {
            $this->configurarRemuneracion($types, $cronograma, $job);
        }

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new BasicNotification("#", "Remuneraciones agregadas a los trabajadores"));
        }
    }


    private function configurarRemuneracion($types, $cronograma, $job)
    {
        $total = 0;
        $mes = $cronograma->mes == 1 ? 12 : $cronograma->mes - 1;
        $year = $cronograma->mes == 1 ? $cronograma->año - 1 : $cronograma->año;
       
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
                    Remuneracion::create([
                        "work_id" => $job->id,
                        "categoria_id" => $info->categoria_id,
                        "cronograma_id" => $cronograma->id,
                        "cargo_id" => $info->cargo_id,
                        "planilla_id" => $info->planilla_id,
                        "type_remuneracion_id" => $remuneracion->id,
                        "mes" => $cronograma->mes,
                        "año" => $cronograma->año,
                        "monto" => round($remuneracion->monto, 2),
                        "adicional" => $cronograma->adicional,
                        "dias" => $cronograma->dias,
                        "base" => $type->base
                    ]);

                    $current_total += $remuneracion->monto;
                }
            }else {
                foreach ($types as $type) {
                    $config = DB::table("concepto_type_remuneracion")
                        ->whereIn("concepto_id", $info->categoria->conceptos->pluck(["id"]))
                        ->where("categoria_id", $info->categoria_id)
                        ->where("type_remuneracion_id", $type->id)
                        ->get();

                    $suma = $config->sum("monto");

                    Remuneracion::create([
                        "work_id" => $job->id,
                        "categoria_id" => $info->categoria_id,
                        "cargo_id" => $info->cargo_id,
                        "planilla_id" => $info->planilla_id,
                        "cronograma_id" => $cronograma->id,
                        "type_remuneracion_id" => $type->id,
                        "mes" => $cronograma->mes,
                        "año" => $cronograma->año,
                        "monto" => \round(($suma * $cronograma->dias) / 30, 2),
                        "adicional" => $cronograma->adicional,
                        "base" => $type->base
                    ]);

                    $current_total += $suma;
                }
            }

            $info->update(["total" => $current_total]);

            $total += $current_total;

        }
        
        $job->update(["total" => round($total)]);

    }

}
