<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use \DB;
use App\Models\User;
use App\Notifications\BasicNotification;
use App\Models\TypeRemuneracion;
use App\Models\Remuneracion;
use App\Models\Info;
use App\Models\Historial;
use App\Models\Cargo;
use App\Collections\RemuneracionCollection;

/**
 * Procesa las remuneraciones de los trabajadores
 */
class ProssingRemuneracion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    
    private $cronograma;
    private $historial;
    private $types;
    private $infoIn = [];
     

    public $timeout = 0;


    /**
     * @param \App\Models\Cronograma $cronograma
     * @param \App\Models\Work $jobs
     * @return void
     */
    public function __construct($cronograma, $infoIn = [])
    {
        $this->cronograma = $cronograma;
        $this->infoIn = $infoIn;
    }

    /**
     * Se ejecuta automaticamente en las colas de trabajos
     *
     * @return void
     */
    public function handle()
    {
        // obtenemos los tipos de remuneracion
        $this->types = TypeRemuneracion::with('categorias')->orderBy("orden", "ASC")->where("activo", 1)->get();
        // cronograma actual
        $cronograma = $this->cronograma;
        // infos associados al cronograma
        $this->historial = Historial::with('categoria')->where("cronograma_id", $cronograma->id);
        // verificar si hay infos especificos
        if (count($this->infoIn)) {
            $this->historial->whereIn("info_id", $this->infoIn);
        }
        // obtener historial
        $this->historial = $this->historial->get();
        // fecha anteriores
        $mes = $cronograma->mes == 1 ? 12 : $cronograma->mes - 1;
        $year = $cronograma->mes == 1 ? $cronograma->año - 1 : $cronograma->año; 
        // obtenemos remuneraciones anteriores
        $typeBefores = Remuneracion::whereIn("info_id", $this->historial->pluck(['info_id']))
            ->whereIn("type_remuneracion_id", $this->types->pluck(['id']))
            ->where("adicional", $cronograma->adicional)
            ->where("mes", $mes)
            ->where("año", $year)
            ->get();
        // guardamos a los trabajadores del mes anterior
        $historialOld = $this->historial->whereIn("info_id", $typeBefores->pluck('info_id'));
        // guardamos a los trabajadores nuevos
        $historialNew = $this->historial->whereNotIn("id", $historialOld->pluck('id'));

        if ($historialNew->count() > 0) {
            $this->configNewRemuneracion($cronograma, $historialNew);
        }

        if ($historialOld->count() > 0) {
            $this->configOldRemuneracion($cronograma, $historialOld, $typeBefores);
        }

    }


    /**
     * Clonar remuneracion del mes anterior
     *
     * @param [type] $cronograma
     * @param [type] $historial
     * @param [type] $befores
     * @return void
     */
    private function configOldRemuneracion($cronograma, $historial, $befores) {
        // creamos la colección para prosesar las remuneraciones
        $config = new RemuneracionCollection($cronograma, $this->types);
        // recorremos a cada trabajador
        foreach ($historial as $history) {
            // recorremos todas los tipos de remuneraciones disponibles
            foreach ($this->types as $type) {
                // verificamos si la configuración de la planilla es conforme
                if ($type->categorias->where("id", $history->categoria_id)->count()) {
                    // obtenermos el registro anterior
                    $typeBefore = $befores->where('info_id', $history->info_id)
                        ->where("type_remuneracion_id", $type->id)    
                        ->first();
                    // almacenamos el monto anterior
                    $monto = isset($typeBefore->monto) ? $typeBefore->monto : 0;
                    // preparamos las remuneraciones para insertalos masivamente
                    $config->preparate($history, $cronograma, $type, $monto);
                }
            }
            // obtener resultado del storage
            $storage = $config->getStorage();
            // guardar base imponible corriente
            $base =  $storage->where('historial_id', $history->id)
                        ->where('base', 0)
                        ->where('show', 1)
                        ->sum('monto');
            // guardar el monto bruto actual
            $bruto = $storage->where('historial_id', $history->id)
                        ->where('show', 1)
                        ->sum('monto');
            // guardar base imponible para el plame
            $base_enc = self::calc_enc($history, $base);
            // actualizar los montos del trabajador
            $history->update([
                "total_bruto" => round($bruto, 2),
                "base" => round($base, 2),
                "base_enc" => round($base_enc, 2)
            ]);
        }
        // insertamos las remuneraciones masivamente
        $config->save();
    }
    

    /**
     * Crear las nuevas remuneraciones por cada trabajador
     *
     * @param [type] $cronograma
     * @param [type] $historial
     * @return void
     */
    private function configNewRemuneracion($cronograma, $historial) {
       // creamos la colección para prosesar las remuneraciones
       $config = new RemuneracionCollection($cronograma, $this->types);
       // insertar masivamente las remuneraciones
       $config->insert($historial);
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
            return $montos->sum();
        }
        // devolvemos la base por defecto
        return $base;
    }


}
