<?php

namespace App\Collections;


use App\Models\Cronograma;
use App\Models\TypeDescuento;
use App\Models\Descuento;
use App\Models\Remuneracion;
use App\Models\Historial;

class DescuentoCollection 
{

    private $cronograma;
    private $storage;
    private static $store;
    private static $types;

    public function __construct(Cronograma $cronograma, $types = [], $store = [])
    {
        $this->cronograma = $cronograma;
        $this->storage = \collect();
        self::$types = $types;
        self::$store = $store;
    }


    public function getStore()
    {
        return self::$store;
    }


    public function create(Cronograma $cronograma, Historial $history, TypeDescuento $type)
    {
        $store = self::config($cronograma, $history, $history);
        return Descuento::create($store->getStore());
    }


    public function update(Cronograma $cronograma, Historial $history, TypeDescuento $type)
    {
        $store = self::config($cronograma, $history, $history);
        $history->update($store->getStore());
        return $history;
    }


    public function preparate(Historial $history, Cronograma $cronograma, TypeDescuento $type, $monto)
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
            "type_descuento_id" => $type->id,
            "mes" => $cronograma->mes,
            "año" => $cronograma->año,
            "dias" => $cronograma->dias,
            "adicional" => $cronograma->adicional,
            "base" => $type->base,
            "edit" => $type->edit,
            "monto" => $monto
        ]);
    }


    public static function config(Cronograma $cronograma, Historial $history, TypeDescuento $type)
    {
        // monto de descuento por defecto
        $monto = 0;
        // verificamos si el type tiene configuracion de afp aporte
        $monto = $type->afp_aportes->count() > 0 ? self::calc_afp_aporte($history, $type) : $monto;
        // verificamos si el type tiene configuracion de afp prima
        $monto = $type->afp_primas->count() > 0 ? self::calc_afp_prima($history, $type) : $monto;
        // verificamos si el type tiene configuracion de type_afp
        $monto = $type->type_afp ? self::calc_type_afp($history, $type) : $monto;
        // verificamos si el type tiene sindicatos asocciados
        $monto = $type->sindicato ? self::calc_sindicato($history, $type) : $monto;
        // almacenamos los resultados
        self::$store = \collect([
            "work_id" => $history->work_id,
            "info_id" => $history->info_id,
            "historial_id" => $history->id,
            "planilla_id" => $history->planilla_id,
            "cargo_id" => $history->cargo_id,
            "categoria_id" => $history->categoria_id,
            "meta_id" => $history->meta_id,
            "cronograma_id" => $cronograma->id,
            "type_descuento_id" => $type->id,
            "mes" => $cronograma->mes,
            "año" => $cronograma->año,
            "adicional" => $cronograma->adicional,
            "base" => $type->base,
            "edit" => $type->edit,
            "monto" => round($monto, 2)
        ]);

        return new self($cronograma, self::$types, self::$store);
    }


    public function gettingRemuneracion($historialIn = [])
    {
        return Remuneracion::where('show', 1)->whereIn('historial_id', $historialIn)->get();
    }


    public function insert($historial)
    {
        $payload = \collect();

        try {
            // obtener todas las remuneraciones de los trabajadores de la planilla actual
            $remuneraciones = self::gettingRemuneracion($historial->pluck(['id']));
            // recorremos a cada trabajador y configuramos sus descuentos
            foreach ($historial as $history) {
                // crear remuneraciones
                foreach (self::$types as $type) {
                    $store = self::config($this->cronograma, $history, $type);
                    $payload->push($store->getStore());
                }
                // obtenemos el total de descuentos
                $total_desct = $payload->where('historial_id', $history->id)
                    ->where('base', 0)
                    ->sum('monto');
                // obtenemos el total de remuneraciones
                $total_bruto = $remuneraciones->where('historial_id', $history->id)->sum('monto');
                // obtenemos el total neto
                $total_neto = $total_bruto - $total_desct;
                // actualizamos el total bruto y el neto
                $history->update([
                    "total_desct" => round($total_desct, 2),
                    "total_neto" => round($total_neto, 2)
                ]);
            }
            // realizamos una inserción masiva
            foreach ($payload->chunk(1000)->toArray() as $insert) {
                Descuento::insert($insert);
            }

        } catch (\Throwable $th) {
            
            \Log::info($th);

        }

    }


    public function save()
    {
        foreach ($this->storage->chunk(1000)->toArray() as $insert) {
            Descuento::insert($insert);
        }
    }


    private static function calc_afp_aporte($history, $type)
    {   
        $afp = $type->afp_aportes->find($history->afp_id);
        // verificamo si el apf no sea null
        if ($afp) {
            return round(($history->base * $afp->aporte) / 100, 2);
        }

        return 0;
    }   


    private static function calc_afp_prima($history, $type)
    {   
        $afp = $type->afp_primas->find($history->afp_id);
        // verificamo si el apf no sea null
        if ($afp) {
            return round(($history->base * $afp->prima) / 100, 2);
        }

        return 0;
    } 


    private static function calc_type_afp($history, $type)
    {
        // obtenemo el tipo de afp asocciado al tipo de descuento
        $type_afp = $type->type_afp;
        // verificamos que no sea null
        if ($type_afp) {
            // verificamo si el trabajador esta en ese tipo de afp
            if ($type_afp->id == $history->type_afp_id) {
                // obtenemos el afp
                $afp = $type_afp->afps->find($history->afp_id);
                // obtenemos el porcentaje
                if ($afp) {
                    $porcentaje = $afp->pivot ? $afp->pivot->porcentaje : 0;
                    return round(($history->base * $porcentaje) / 100, 2);
                }
            }
        }
        // retornamos cero 
        return 0;
    }


    private static function calc_sindicato($history, $type)
    {
        // verificamos que el trabajadore esta en un sindicato
        if ($history->sindicato_id) {
            // obtenemos el sindicato
            $sindicato = $type->sindicato;
            // verificamos que no sea null
            if ($sindicato->id == $history->sindicato_id) {
                return round(($history->base * $sindicato->porcentaje) / 100 , 2);
            }
        }
        // retornar cero
        return 0;
    }

}