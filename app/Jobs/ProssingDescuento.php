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
use App\Models\Remuneracion;
use App\Models\User;
use App\Notifications\BasicNotification;
use App\Models\Sindicato;

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

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new BasicNotification("#", "Descuentos agregados a los trabajadores", "fas fa-file-alt", "bg-danger"));
        }

    }

    private function configurarDescuento($types, $cronograma, $job)
    {
        $mes = $cronograma->mes == 1 ? 12 : $cronograma->mes - 1;
        $year = $cronograma->mes == 1 ? $cronograma->año - 1 : $cronograma->año; 
        
        foreach ($job->infos as $info) {
            $hasDescuentos = Descuento::where("work_id", $job->id)
                ->where("cronograma_id", $cronograma->id)
                ->where("cargo_id", $info->cargo_id)
                ->where("categoria_id", $info->categoria_id)
                ->where("planilla_id", $info->planilla_id)
                ->get();

            if($hasDescuentos->count() > 0) {
                foreach ($hasDescuentos as $descuento) {
                    Descuento::create([
                        "work_id" => $job->id,
                        "categoria_id" => $info->categoria_id,
                        "cargo_id" => $info->cargo_id,
                        "cronograma_id" => $cronograma->id,
                        "planilla_id" => $info->planilla_id,
                        "type_descuento_id" => $descuento->id,
                        "mes" => $cronograma->mes,
                        "año" => $cronograma->año,
                        "monto" => $descuento->monto,
                        "adicional" => $cronograma->adicional,
                        "dias" => $cronograma->dias
                    ]);
                }
            }else {
                foreach ($types as $type) {
                    $config = \json_decode($type->config_afp);

                    $remuneraciones = Remuneracion::where('work_id', $job->id)
                        ->where('cargo_id', $info->cargo_id)
                        ->where('categoria_id', $info->categoria_id)
                        ->where('cronograma_id', $cronograma->id)
                        ->where('planilla_id', $info->planilla_id)
                        ->where('base', 0)
                        ->get();

                    
                    $total = $remuneraciones->sum("monto");
                    $total = $total > 0 ? $total : $info->total;
                    $suma = 0;
                    
                    //configurar descuentos automaticos de afps
                    if($job->afp) {

                        if(\is_array($config) && count($config) > 0) {
                            $type_afp = "";
                            $opcional = count($config) > 1 ? true : false; 

                            if($opcional) {

                                foreach ($config as $conf) {
                                    if($conf != "aporte" && $conf != "prima") {
                                        if($conf == $job->type_afp) {
                                            $type_afp = $conf;
                                            break;
                                        }
                                    }
                                }

                            }else {
                                $type_afp = implode("", $config);
                            }

                            $porcentaje = $job->afp->{$type_afp};
                            $suma = ($total * $porcentaje) / 100;

                        }
                    }elseif ($job->ley) {

                        if($type->ley) {
                            $suma = $total * 0.06;
                        }

                    }else {

                        if($type->obligatorio) {
                            $suma =($total * 13) / 100;   
                        }

                    }


                    // configurar descuentos automaticos de los sindicatos
                    if ($job->sindicatos->count() > 0) {

                        // obtenemos la configuracion de los sindicatos que tiene el descuento
                        $sindicatoIn = $type->sindicatos->pluck(["id"]);

                        if (count($sindicatoIn) > 0) {

                            //almacenar el porcentaje de los sindicatos de la configuración
                            $porcentaje_sindicato = 0;
    
                            //obtener los porcentajes de los sindicatos del trabajador
                            $porcentaje_sindicato = $job->sindicatos->whereIn("id", $sindicatoIn)->sum('porcentaje');
    
                            // terminamos la configuracion de los sindicatos
                            $suma =  ($total * $porcentaje_sindicato) / 100;

                        }


                    }

                    
                    Descuento::create([
                        "work_id" => $job->id,
                        "categoria_id" => $info->categoria_id,
                        "cargo_id" => $info->cargo_id,
                        "planilla_id" => $info->planilla_id,
                        "cronograma_id" => $cronograma->id,
                        "type_descuento_id" => $type->id,
                        "mes" => $cronograma->mes,
                        "año" => $cronograma->año,
                        "monto" => round($suma, 2),
                        "adicional" => $cronograma->adicional,
                        "dias" => $cronograma->dias
                    ]);


                }
            }
        }

    }

}
