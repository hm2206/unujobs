<?php

namespace App\Collections;


use App\Models\Descuento;
use App\Models\TypeDescuento;
use App\Models\Remuneracion;
use App\Models\TypeRemuneracion;
use \DB;

class InfoCollection 
{

    private $info;
    private $type_remuneraciones;
    private $type_descuentos;

    public function __construct($info)
    {
        $this->info = $info;
    }

    public function updateOrCreateDescuento($cronograma, $type_descuentos, $remuneraciones_base)
    {   
        $info = $this->info;
        $work = $info->work;
        $types =  $type_descuentos;

        try {
                
            $base = 0;

            foreach ($types as $type) {

                $config = \json_decode($type->config_afp);

                // Crear o actualizar descuento
                $newDescuento = Descuento::updateOrCreate([
                    "work_id" => $info->work_id,
                    "info_id" => $info->id,
                    "categoria_id" => $info->categoria_id,
                    "cargo_id" => $info->cargo_id,
                    "planilla_id" => $info->planilla_id,
                    "cronograma_id" => $cronograma->id,
                    "type_descuento_id" => $type->id,
                    "mes" => $cronograma->mes,
                    "año" => $cronograma->año,
                    "adicional" => $cronograma->adicional,
                    "dias" => $cronograma->dias,
                    "base" => $type->base,
                    "meta_id" => $info->meta_id,
                ]);


                    $total = round($remuneraciones_base->sum("monto"), 2);
                    $total = $total > 0 ? $total : round($info->total, 2);
                    $suma = 0;
                    
                    // configurar descuentos automaticos de los sindicatos
                    if ($work->sindicatos->count() > 0) {
                        // obtenemos la configuracion de los sindicatos que tiene el descuento
                        $sindicatoIn = $type->sindicatos->pluck(["id"]);
                        
                        if (count($sindicatoIn) > 0) {
                            
                            //almacenar el porcentaje de los sindicatos de la configuración
                            $porcentaje_sindicato = 0;
                            
                            //obtener los porcentajes de los sindicatos del trabajador
                            $porcentaje_sindicato = $work->sindicatos->whereIn("id", $sindicatoIn)->sum('porcentaje');
                            
                            // terminamos la configuracion de los sindicatos
                            $suma =  round(($total * $porcentaje_sindicato) / 100, 2);
                            $tmp_total = $work->descanso ? 0 : round($suma, 2);
                            // guardamos los datos
                            $newDescuento->monto = $tmp_total;
                            $newDescuento->edit = $type->edit;
                            $newDescuento->save();
                            
                            // continuamos
                            continue;

                        }

                    }

                        
                    // verificamos que el trabajador esta afecto a retenciones por ley
                    if ($work->afecto) {

                        //configurar descuentos automaticos de afps
                        if($work->afp) {

                            if(\is_array($config) && count($config) > 0) {
                                $type_afp = "";
                                $opcional = count($config) > 1 ? true : false; 

                                if($opcional) {

                                    foreach ($config as $conf) {
                                        if($conf != "aporte" && $conf != "prima") {
                                            if($conf == $work->type_afp) {
                                                $type_afp = $conf;
                                                break;
                                            }
                                        }
                                    }

                                }else {
                                    $type_afp = implode("", $config);
                                }

                                $porcentaje = $work->afp->{$type_afp};
                                $suma = round(($total * $porcentaje) / 100, 2);

                                // guardamos los datos
                                $newDescuento->monto = $work->descanso ? 0 : round($suma, 2);
                                $newDescuento->edit = $type->edit;
                                $newDescuento->save();
                                
                                // continuamos
                                continue;

                            }

                        }

                        // obtenemos los descuentos que se puede aplicar al trabajador
                        $work_descuento = $work->typeDescuentos()
                            ->where("base", 0)
                            ->where("id", $type->id)->first();
                                
                        //verificamos que el trabajador tiene un descuento especifico
                        if ($work_descuento) {

                            // verificamos que tenga una confguración
                            if ($type->config) {
                                $config = $type->config;
                                // preguntamos, si la configuración tiene un limite
                                $suma = $config->minimo <= $total  
                                    ? round(($total * $config->porcentaje) / 100, 2)
                                    : round($config->monto, 2);
                                    
                                // guardamos los datos
                                $newDescuento->monto = $work->descanso ? 0 : round($suma, 2);
                                $newDescuento->edit = $type->edit;
                                $newDescuento->save();
                                
                                // continuamos
                                continue;

                            }
                        }
                    }


                    // obtenemos los aportes del empleador
                    $work_aporte = $work->typeDescuentos()
                        ->where("base", 1)
                        ->where("id", $type->id)
                        ->first();
                        
                    //verificamos que el trabajador tiene un descuento especifico
                    if ($work_aporte) {
                        // verificamos que tenga una confguración
                        if ($type->config) {
                            $config = $type->config;
                            // preguntamos, si la configuración tiene un limite
                            $suma = $config->minimo <= $total  
                                ? round(($total * $config->porcentaje) / 100, 2)
                                : round($config->monto, 2); 

                            // guardamos los datos
                            $newDescuento->monto = $work->descanso ? 0 : round($suma, 2);
                            $newDescuento->edit = $type->edit;
                            $newDescuento->save();
                            
                            // continuamos
                            continue;
                        }
                    }else {

                        if ($type->base >= 1) {

                            $newDescuento->monto = 0;
                            $newDescuento->edit = $type->edit;
                            $newDescuento->save();

                            // continuamos
                            continue;
                        }

                    }
            
            }

        } catch (\Throwable $th) {
            
            \Log::info($th);

        }
    }


    public function createOrUpdateRemuneracion($cronograma, $type_remuneraciones)
    {
        $total = 0;
        $info = $this->info;
        $work = $info->work;
        $types = $type_remuneraciones;
        
        try {

            $current_total = 0;            
                
            foreach ($types as $type) {
                $config = DB::table("concepto_type_remuneracion")
                    ->whereIn("concepto_id", $info->categoria->conceptos->pluck(["id"]))
                    ->where("categoria_id", $info->categoria_id)
                    ->where("type_remuneracion_id", $type->id)
                    ->get();
    
                $suma = $config->sum("monto");
    
                $current_monto = $work->descanso ? 0 : \round(($suma * $cronograma->dias) / 30, 2);
    
                // Actualizar o crear una remuneración
                $newRemuneracion = Remuneracion::updateOrCreate([
                    "work_id" => $work->id,
                    "info_id" => $info->id,
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

            }


        } catch (\Throwable $th) {
            
            \Log::info($th);
        }

    }


}