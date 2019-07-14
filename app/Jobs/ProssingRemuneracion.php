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
        $hasRemuneraciones = Remuneracion::where("work_id", $job->id)
            ->where("mes", $mes)->where("año", $year)->get();

            
        if ($hasRemuneraciones->count() > 0) {
            foreach ($hasRemuneraciones as $remuneracion) {
                Remuneracion::create([
                    "work_id" => $job->id,
                    "categoria_id" => $job->categoria_id,
                    "cronograma_id" => $cronograma->id,
                    "cargo_id" => $job->cargo_id,
                    "type_remuneracion_id" => $remuneracion->id,
                    "mes" => $cronograma->mes,
                    "año" => $cronograma->año,
                    "monto" => round($remuneracion->monto, 2),
                    "adicional" => $cronograma->adicional,
                    "base" => $type->base
                ]);

                $total += $remuneracion->monto;
            }
        }else {
            foreach ($types as $type) {
                $config = DB::table("concepto_type_remuneracion")
                    ->whereIn("concepto_id", $job->categoria->conceptos->pluck(["id"]))
                    ->where("categoria_id", $job->categoria->id)
                    ->where("type_remuneracion_id", $type->id)
                    ->get();
                $suma = $config->sum("monto");
                Remuneracion::create([
                    "work_id" => $job->id,
                    "categoria_id" => $job->categoria_id,
                    "cargo_id" => $job->cargo_id,
                    "cronograma_id" => $cronograma->id,
                    "type_remuneracion_id" => $type->id,
                    "mes" => $cronograma->mes,
                    "año" => $cronograma->año,
                    "monto" => \round(($suma * $cronograma->dias) / 30, 2),
                    "adicional" => $cronograma->adicional,
                    "base" => $type->base
                ]);

                $total += $suma;
            }
        }

        $job->update(["total" => round($total)]);
    }

}
