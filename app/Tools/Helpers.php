<?php

namespace App\Tools;
use App\Models\Historial;
use App\Models\Cronograma;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Models\TypeRemuneracion;
use App\Collections\RemuneracionCollection;

class Helpers 
{

    /**
     * Buscar el historial de la plaza
     *
     * @param [type] $plaza
     * @return bool
     */
    public static function historialPlazaDisponible($plaza, $id, $mes, $year)
    {   
        // obtener los cronogramas de ese {$mes} y {$year}
        $cronogramas = Cronograma::where('mes', $mes)->where('año', $year)->get(['id']);
        // realizar busqueda de la plaza
        $historial = Historial::where("id", "<>", $id)
            ->whereIn('cronograma_id', $cronogramas->pluck(['id']))
            ->count();
        // verificar si está disponible
        if ($historial) {
            // plaza ocupada
            return false;
        }
        // retornar que la plaza está disponible
        return true;
    }


    /**
     * Cambiar categoria
     *
     * @return void
     */
    public static function changeCategoria(Historial $history, Cronograma $cronograma, $newCategoria)
    {
        // verificamos que no se realizó ningún cambio ne la categoria
        if ($history->categoria_id == $newCategoria) {
            return false;
        }
        // actualizamos la categoria
        $history->update(["categoria_id" => $newCategoria]);
        // eliminamos las remuneraciones anteriores
        Remuneracion::where("historial_id", $history->id)->delete();
        // obtener los tipos de remuneraciones
        $types = TypeRemuneracion::where('activo', 1)->get();
        // crear los campos de las remuneraciones
        RemuneracionCollection::create($history, $cronograma, $types);
        // devolver que se realizó el cambio de categoria
        return true;
    } 


}