<?php

namespace App\Collections;


use App\Models\Descuento;
use App\Models\TypeDescuento;
use App\Models\Remuneracion;
use App\Models\TypeRemuneracion;
use \DB;

class WorkCollection 
{

    private $work;
    private $type_remuneraciones;
    private $type_descuentos;

    public function __construct($work)
    {
        $this->work = $work;
        $this->type_remuneraciones = TypeRemuneracion::where("activo", 1)->get();
        $this->type_descuentos = TypeDescuento::where("activo", 1)->get();
    }

    public function updateOrCreateDescuento($cronograma)
    {   

        $mes = $cronograma->mes == 1 ? 12 : $cronograma->mes - 1;
        $year = $cronograma->mes == 1 ? $cronograma->año - 1 : $cronograma->año; 

        $work = $this->work;
        $types =  $this->type_descuentos;

        try {

            foreach ($work->infos as $info) {
                
                $base = 0;

                $hasDescuentos = Descuento::where("work_id", $work->id)
                    ->where("cargo_id", $info->cargo_id)
                    ->where("categoria_id", $info->categoria_id)
                    ->where("planilla_id", $info->planilla_id)
                    ->where("mes", $mes)
                    ->where("año", $year)
                    ->where("adicional", $cronograma->adicional)
                    ->get();

                if($hasDescuentos->count() > 0) {
                    foreach ($hasDescuentos as $descuento) {
                        // Crear o actualizar descuento
                        Descuento::create([
                            "work_id" => $work->id,
                            "categoria_id" => $info->categoria_id,
                            "cargo_id" => $info->cargo_id,
                            "planilla_id" => $info->planilla_id,
                            "cronograma_id" => $cronograma->id,
                            "type_descuento_id" => $type->id,
                            "mes" => $cronograma->mes,
                            "año" => $cronograma->año,
                            "monto" => $work->descanso ? 0 : round($descunto->monto, 2),
                            "adicional" => $cronograma->adicional,
                            "dias" => $cronograma->dias,
                            "base" => $type->base,
                            "meta_id" => $info->meta_id,
                            "edit" => $descuento->edit
                        ]);
                    }
                }else {
                    foreach ($types as $type) {
                        $config = \json_decode($type->config_afp);

                        $remuneraciones = Remuneracion::where('work_id', $work->id)
                            ->where('cargo_id', $info->cargo_id)
                            ->where('categoria_id', $info->categoria_id)
                            ->where('cronograma_id', $cronograma->id)
                            ->where('planilla_id', $info->planilla_id)
                            ->where('base', 0)
                            ->get();

                        
                        $total = $remuneraciones->sum("monto");
                        $total = $total > 0 ? $total : $info->total;
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
                                $suma =  ($total * $porcentaje_sindicato) / 100;

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
                                    $suma = ($total * $porcentaje) / 100;

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
                                        ? ($total * $config->porcentaje) / 100 
                                        : $config->monto; 
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
                                ? ($total * $config->porcentaje) / 100 
                                : $config->monto; 
                            }
                        }



                        // Crear o actualizar descuento
                        $newDescuento = Descuento::updateOrCreate([
                            "work_id" => $work->id,
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


                        $newDescuento->update([
                            "monto" => $work->descanso ? 0 : round($suma, 2),
                            "edit" => $type->edit
                        ]);



                    }
                }
            }

        } catch (\Throwable $th) {
            
            \Log::info($th);

        }
    }


    public function createOrUpdateRemuneracion($cronograma)
    {
        $total = 0;
        $work = $this->work;
        $mes = $cronograma->mes == 1 ? 12 : $cronograma->mes - 1;
        $year = $cronograma->mes == 1 ? $cronograma->año - 1 : $cronograma->año;
        $types = $this->type_remuneraciones;
       
        try {

            foreach ($work->infos as $info) {
    
                $current_total = 0;
    
                $hasRemuneraciones = Remuneracion::where("work_id", $work->id)
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
    
                        $current_monto = $work->descanso ? 0 : round($remuneracion->monto, 2);
    
                        Remuneracion::updateOrCreate([
                            "work_id" => $work->id,
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
    
                        $current_monto = $work->descanso ? 0 : \round(($suma * $cronograma->dias) / 30, 2);
    
                        Remuneracion::updateOrCreate([
                            "work_id" => $work->id,
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