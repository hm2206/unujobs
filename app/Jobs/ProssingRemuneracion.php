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

class ProssingRemuneracion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    
    private $cronograma;
     

    public function __construct(Cronograma $cronograma)
    {
        $this->cronograma = $cronograma;
    }


    public function handle()
    {
        $cronograma = $this->cronograma;
        $jobs = Work::where('planilla_id', $cronograma->planilla_id)->get();
        $types = TypeRemuneracion::all();

        foreach ($jobs as $job) {
            $this->configurarRemuneracion($types, $cronograma, $job);
        }
    }


    private function configurarRemuneracion($types, $cronograma, $job)
    {
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
                    "type_remuneracion_id" => $remuneracion->id,
                    "mes" => $cronograma->mes,
                    "año" => $cronograma->año,
                    "monto" => $remuneracion->monto,
                    "adicional" => $cronograma->adicional
                ]);
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
                    "cronograma_id" => $cronograma->id,
                    "type_remuneracion_id" => $type->id,
                    "mes" => $cronograma->mes,
                    "año" => $cronograma->año,
                    "monto" => $suma,
                    "adicional" => $cronograma->adicional
                ]);
            }
        }
    }

}
