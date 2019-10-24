<?php

namespace App\Collections;

use App\Models\Remuneracion;
use App\Models\TypeRemuneracion;
use App\Models\Historial;
use App\Models\Cronograma;


class RemuneracionCollection {


    private $types;
    private $cronograma;
    private $storage;


    public function __construct(Cronograma $cronograma, $types)
    {
        $this->cronograma = $cronograma;
        $this->storage = \collect();
        $this->types = $types;
    }


    public function preparate(Historial $history, Cronograma $cronograma, TypeRemuneracion $type, $monto)
    {
        $this->storage->push([
            "work_id" => $history->work_id,
            "info_id" => $history->info_id,
            "historial_id" => $history->id,
            "planilla_id" => $history->planilla_id,
            "cargo_id" => $history->cargo_id,
            "categoria_id" => $history->categoria_id,
            "meta_id" => $history->meta_id,
            "cronograma_id" => $cronograma->id,
            "type_remuneracion_id" => $type->id,
            "mes" => $cronograma->mes,
            "año" => $cronograma->año,
            "adicional" => $cronograma->adicional,
            "base" => $type->base,
            "show" => $type->show,
            "monto" => round($monto, 2)
        ]);
    }


    public function save()
    {
        foreach ($this->storage->chunk(1000) as $insert) {
            Remuneracion::insert($insert);
        }
    }


    public function insert($historial)
    {
        $payload = \collect();

        try {

            foreach ($historial as $history) {
                // crear remuneraciones
                foreach ($this->types as $index => $type) {
                    // obtener los conceptos por ley
                    $conceptos = $type->conceptos()
                        ->wherePivot('categoria_id', $history->categoria_id)
                        ->get();
                    // obtener los montos de los conceptos
                    $montos = $conceptos->map(function($con) {
                        return $con->config ? $con->config->monto : 0;
                    });
                    // sumamos los conceptos 
                    $suma = $montos->sum();
                    // guardamos el monto actual
                    $current_monto = $history->afecto ? \round(($suma * $this->cronograma->dias) / 30, 2) : 0;
                    // verificar el calculo de tipo de remuneracion relacionado
                    $current_monto = self::calc_type_remuneracion($history, $type, $payload, $current_monto);
                    // almacenamos los resultados
                    $payload->push([
                        "work_id" => $history->work_id,
                        "info_id" => $history->info_id,
                        "historial_id" => $history->id,
                        "planilla_id" => $history->planilla_id,
                        "cargo_id" => $history->cargo_id,
                        "categoria_id" => $history->categoria_id,
                        "meta_id" => $history->meta_id,
                        "cronograma_id" => $this->cronograma->id,
                        "type_remuneracion_id" => $type->id,
                        "mes" => $this->cronograma->mes,
                        "año" => $this->cronograma->año,
                        "adicional" => $this->cronograma->adicional,
                        "base" => $type->base,
                        "show" => $type->show,
                        "monto" => round($current_monto, 2)
                    ]);
                }
                // obtenemos el monto bruto del trbajador
                $bruto = $payload->where("historial_id", $history->id)
                    ->where('show', 1)
                    ->sum('monto');
                // obtenemos la base imponible
                $base = $payload->where("historial_id", $history->id)
                    ->where('show', 1)
                    ->where('base', 0)
                    ->sum('monto');
                // base enc
                $base_enc = self::calc_enc($history, $base);
                // actualizar el monto
                $history->update([
                    "total_bruto" => round($bruto, 2),
                    "base" => round($base, 2)
                ]);
            }

            // realizamos una inserción masiva
            foreach ($payload->chunk(1000)->toArray() as $insert) {
                Remuneracion::insert($insert);
            }

        } catch (\Throwable $th) {
            
            \Log::info($th);

        }

    }


    private function calc_enc($history, $base)
    {
        // obtenemos la categoria del trabajador
        $categoria = $history->categoria;
        // verificamos si el trabajador solo tiene un solo concepto
        if ($categoria->conceptos->count() == 1 ) {
            // obtenemos los montos del los conceptos
            $montos = $categoria->conceptos->map(function($con) {
                return $con->config ? $con->config->monto : 0;
            });
            // sumamos los conceptos 
            return round($montos->sum(), 2);
        }
        // devolvemos la base por defecto
        return round($base, 2);
    }


    private function calc_type_remuneracion($history, $type, $payload, $current)
    {
        if ($type->type_remuneraciones->count() > 0) {
            // obtenemos los montos
            $monto = $payload->whereIn('type_remuneracion_id', $type->type_remuneraciones->pluck(['id']))
                ->where('historial_id', $history->id)
                ->sum('monto');
            return round($monto, 2);
        }

        return round($current, 2);
    }


    public function getStorage()
    {   
        return $this->storage;
    }
    
}