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
use App\Models\TypeDescuento;
use App\Models\Descuento;
use App\Models\Afp;
use App\Models\Cargo;
use App\models\Remuneracion;

class ProssingDescuento implements ShouldQueue
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
        $types = TypeDescuento::all();

        foreach ($jobs as $job) {
            $this->configurarDescuento($types,$cronograma, $job);
        }
    }

    private function configurarDescuento($types, $cronograma, $job)
    {
        $mes = $cronograma->mes == 1 ? 12 : $cronograma->mes - 1;
        $year = $cronograma->mes == 1 ? $cronograma->año - 1 : $cronograma->año; 
        $hasDescuentos = Descuento::where("work_id", $job->id)
            ->where("mes", $mes)->where("año", $year)->get();
        if($hasDescuentos->count() > 0) {
            foreach ($hasDescuentos as $descuento) {
                Descuento::create([
                    "work_id" => $job->id,
                    "categoria_id" => $job->categoria_id,
                    "cronograma_id" => $cronograma->id,
                    "type_descuento_id" => $descuento->id,
                    "mes" => $cronograma->mes,
                    "año" => $cronograma->año,
                    "monto" => $descuento->monto,
                    "adicional" => $cronograma->adicional
                ]);
            }
        }else {
            foreach ($types as $type) {
                $config = \json_decode($type->config_afp);
                $suma = 0;
                
                if($job->afp) {

                    if(\is_array($config) && count($config) > 0) {
                        $type_afp = "";
                        $opcional = count($config) > 1 ? true : false; 

                        if($opcional) {
                            foreach ($config as $conf) {
                                if($conf != "aporte" && $conf != "prima") {
                                    $type_afp = $conf == $job->type_afp ? $conf : null;
                                    break;
                                }
                            }

                        }else {
                            $type_afp = implode("", $config);
                        }


                        if ($type_afp) {

                            $porcentaje = $job->afp->{$type_afp};
                            $total = 0;

                            $cargo = $job->cargo;
                            $typeIn = $cargo->typeRemuneracions->pluck(["id"]);
                            $remuneraciones = Remuneracion::where('work_id', $job->id)
                                ->where('cargo_id', $job->cargo_id)
                                ->where('categoria_id', $job->categoria_id)
                                ->where('cronograma_id', $cronograma->id)
                                ->whereIn('type_remuneracion_id', $typeIn)
                                ->get();

                            $total = $remuneraciones->suma("monto");
                            $suma = ($total * $porcentaje) / 100;

                        }


                    }
                }

                
                Descuento::create([
                    "work_id" => $job->id,
                    "categoria_id" => $job->categoria_id,
                    "cronograma_id" => $cronograma->id,
                    "type_descuento_id" => $type->id,
                    "mes" => $cronograma->mes,
                    "año" => $cronograma->año,
                    "monto" => $suma,
                    "adicional" => $cronograma->adicional
                ]);


            }
        }
    }

}
