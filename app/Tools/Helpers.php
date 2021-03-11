<?php

namespace App\Tools;
use App\Models\Historial;
use App\Models\Cronograma;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Models\TypeRemuneracion;
use App\Collections\RemuneracionCollection;
use App\Models\Report;

class Helpers 
{

    private static $ruleReport = [
        "key", "name", "pendiente", "path", "icono", "type", "read"
    ];

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
        $history = Historial::with('work')->where("id", "<>", $id)
            ->whereIn('cronograma_id', $cronogramas->pluck(['id']))
            ->where('plaza', $plaza)
            ->first();
        // verificar si está disponible
        if ($history) {
            // datos del ocupante de la plaza
            $name = $history->work ? $history->work->nombre_completo : '';
            // plaza ocupada
            return [
                "success" => false,
                "message" => "La plaza está ocupada por: {$name}"
            ];
        }
        // retornar que la plaza está disponible
        return [
            "success" => true,
            "message" => "plaza disponible!"
        ];
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
        // actualizamos los descuentos
        Descuento::where('historial_id', $history->id)->update([
            "cargo_id" => $history->cargo_id,
            "categoria_id" => $history->categoria_id,
            "meta_id" => $history->meta_id
        ]);
        // devolver que se realizó el cambio de categoria
        return true;
    } 


    /**
     * Verificar y realizar cambios de las metas en las remuneraciones y descuentos de los trabajadores
     *
     * @param Historial $history
     * @param [type] $newMeta
     * @return void
     */
    public static function changeMeta(Historial $history, $newMeta)
    {
        // verificamos que la meta no se modifico
        if ($newMeta == $history->meta_id) {
            return false;
        }
        // actualizar las metas de las remuneraciones
        Remuneracion::where('historial_id', $history->id)->update(["meta_id" => $newMeta ]);
        // actualizar las metas de los descuentos
        Descuento::where('historial_id', $history->id)->update(["meta_id" => $newMeta]);
        return true;
    }



    public static function generateReport($type, $cronograma)
    {
        $archivo = Report::where("cronograma_id", $cronograma)
            ->where("type_report_id", $type)
            ->where('pendiente', 1)
            ->count();
        // verificamos si reporte esta pendiente
        if ($archivo) {
            return [
                "success" => false,
                "message" => "El Reporte aún se encuentra procesando..."
            ];
        }
        // devolvemos que el reporte se puede generar
        return [
            "success" => true,
            "message" => "El reporte está siendo procesado, vuelva luego",
        ];
    }


    public static function createOrUpdateReport(Cronograma $cronograma, $type_report, array $config = []) 
    {
        // validamos las reglas
        foreach (self::$ruleReport as $rule) {
            if (!isset($config[$rule])) {
                throw new Exception("La clave no está registrado en las reglas de los reportes", 1);
                break; 
            }
        }
        // buscarmos si el reporte existe
        $archivo = Report::where("cronograma_id", $cronograma->id)
            ->where("type_report_id", $type_report)
            ->where("key", $config['key'])
            ->first();
        // actualizamos el reporte
        if ($archivo) {
            $archivo->update([
                "type" => $config['type'],
                "icono" => $config['icono'],
                "key" => $config['key'],
                "name" => $config['name'],
                "read" => $config['read'],
                "path" => $config['path'],
                "pendiente" => $config['pendiente']
            ]);
        }else {
            $archivo = Report::create([
                "type" => $config['type'],
                "key" => $config['key'],
                "name" => $config['name'],
                "icono" => $config['icono'],
                "path" => $config['path'],
                "cronograma_id" => $cronograma->id,
                "type_report_id" => $type_report,
                "pendiente" => $config['pendiente']
            ]);
        }
        // devolvemos el archivo creado
        return $archivo;
    }

}