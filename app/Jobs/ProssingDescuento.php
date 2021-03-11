<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use \DB;
use App\Models\Work;
use App\Models\Info;
use App\Models\Cronograma;
use App\Models\User;
use App\Notifications\BasicNotification;
use App\Collections\InfoCollection;
use App\Models\TypeDescuento;
use App\Models\Descuento;
use App\Models\Remuneracion;
use App\Models\Historial;
use App\Collections\DescuentoCollection;

/**
 * Procesa los descuentos de los trabajadores
 */
class ProssingDescuento implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $cronograma;
    private $historial;
    private $types;
    private $infoIn = [];

    public $timeout = 0;

    /**
     * @param object $cronograma
     * @param object $jobs
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
        // cronograma actual
        $cronograma = $this->cronograma;
        // obtener tipos de descuentos
        $this->types = TypeDescuento::where("activo", 1)->get();
        // obtener a todos los trabajadores
        $this->historial = Historial::where("cronograma_id", $cronograma->id);
        // verificar si hay infos especificos
        if (count($this->infoIn)) {
            $this->historial = $this->historial->whereIn("info_id", $this->infoIn);
        }
        // obtener historial
        $this->historial = $this->historial->get();
        // fecha anteriores
        $mes = $cronograma->mes == 1 ? 12 : $cronograma->mes - 1;
        $year = $cronograma->mes == 1 ? $cronograma->año - 1 : $cronograma->año; 
        // obtenemos remuneraciones anteriores
        $typeBefores = Descuento::whereIn("info_id", $this->historial->pluck(['info_id']))
            ->whereIn("type_descuento_id", $this->types->pluck(['id']))
            ->where("adicional", $cronograma->adicional)
            ->where("mes", $mes)
            ->where("año", $year)
            ->get();

        // obtener a los trabajadores con los descuentos del mes anterior
        $oldHistorial = $this->historial->whereIn("info_id", $typeBefores->pluck(['info_id']));
        // obtener a los trabajadores recientemente agregados
        $newHistorial = $this->historial->whereNotIn("id", $oldHistorial->pluck(['id']));


        // configurar descuentos
        if ($oldHistorial->count() > 0) {
            $this->configOldDescuento($cronograma, $oldHistorial, $typeBefores);
        }

        //configurar descuentos nuevos
        if ($newHistorial->count() > 0) {
            $this->configNewDescuento($cronograma, $newHistorial);
        }

        // habilitamos la planilla
        $cronograma->update([
            "pendiente" => 0,
            "estado" => 1
        ]);

        $users = User::all();
        $link = "/planilla/cronograma/{$cronograma->slug()}/job";
        $titulo = $cronograma->planilla ? $cronograma->planilla->descripcion : '';
        $message = $titulo . " del {$cronograma->mes} - {$cronograma->año}, ya está lista";

        foreach ($users as $user) {
            $user->notify(new BasicNotification($link, $message,
                 "fas fa-file-alt", "bg-danger"));
        }

    }


    private function configOldDescuento($cronograma, $historial, $befores) 
    {
        // creamos la collecion para procesar los descuentos
        $config = new DescuentoCollection($cronograma, $this->types);
        // recorremos los trabajadores
        foreach ($historial as $history) {
            // recorremos lso tipos de descuentos
            foreach ($this->types as $type) {
                // obtenemos el registro anterior
                $typeBefore = $befores->where('info_id', $history->info_id)
                    ->where("type_descuento_id", $type->id)
                    ->first();
                // almacenamos el monto
                $monto = isset($typeBefore->monto) ? $typeBefore->monto : 0;
                // preparamos los descuentos para calcular
                $config->preparate($history, $cronograma, $type, $monto);
            }
            // obtenemos el total de descuentos
            $total_desct = $befores->where('info_id', $history->info_id)->sum('monto');
            // obtener total neto
            $total_neto = $history->total_bruto - $total_desct;
            // actualizar historial
            $history->update([
                "total_desct" => round($total_desct, 2),
                "total_neto" => round($total_neto, 2)
            ]);
        }
        // insertamos masivamente los descuentos
        $config->save();
    }


    private function configNewDescuento($cronograma, $historial)
    {
        // creamos la collecion para procesar los descuentos
        $config = new DescuentoCollection($cronograma, $this->types);
        // insertamos masivamente los descuentos
        $config->insert($historial);
    }


}
