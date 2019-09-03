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

/**
 * Procesa los descuentos de los trabajadores
 */
class ProssingDescuento implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $cronograma;
    private $infos;
    private $infoIn;
    private $types;

    public $timeout = 0;

    /**
     * @param object $cronograma
     * @param object $jobs
     * @return void
     */
    public function __construct($cronograma, $infoIn)
    {
        $this->cronograma = $cronograma;
        $this->infoIn = $infoIn;
        $this->infos = Info::with("work")->whereIn("id", $infoIn)->get();
        $this->types = TypeDescuento::where("activo", 1)->get();
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
        // fecha anteriores
        $mes = $cronograma->mes == 1 ? 12 : $cronograma->mes - 1;
        $year = $cronograma->mes == 1 ? $cronograma->año - 1 : $cronograma->año; 
        // obtenemos remuneraciones anteriores
        $typeBefores = Descuento::whereIn("info_id", $this->infoIn)
            ->whereIn("type_descuento_id", $this->types->pluck(['id']))
            ->where("adicional", $cronograma->adicional)
            ->where("mes", $mes)
            ->where("año", $year)
            ->get();

        $bases = Remuneracion::where("base", 0)
            ->where("cronograma_id", $cronograma->id)
            ->whereIn("info_id", $this->infoIn)
            ->get();

        foreach ($this->infos as $info) {
            $this->configurarDescuento($cronograma, $info, $typeBefores, $bases);
        }

        // habilitamos la planilla
        $cronograma->update(["pendiente" => 0]);

        $users = User::all();
        $link = "/planilla/cronograma/{$cronograma->slug()}/job";
        $titulo = $cronograma->planilla ? $cronograma->planilla->descripcion : '';
        $message = $titulo . " del {$cronograma->mes} - {$cronograma->año}, ya está lista";

        foreach ($users as $user) {
            $user->notify(new BasicNotification($link, $message,
                 "fas fa-file-alt", "bg-danger"));
        }

    }


    /**
     * Se encarga de configurar y procesar los descuentos de cada trabajador
     *
     * @param \App\Models\TypeDescuento $types
     * @param \App\Models\Cronograma $cronograma
     * @param \App\Models\Work $job
     * @return void
     */
    private function configurarDescuento($cronograma, $info, $typeBefores, $bases)
   {
        // obtener descuentos de un mes anterior
        $isTypeBefore = $typeBefores->where("info_id", $info->id);
        // verificamos si hay un registro anterior
        if ($isTypeBefore->count()) {
            // recorremos los tipos de remuneraciones
            foreach ($this->types as $type) {
                // obtenemos un tipo de remuneracion especifica
                $isType = $isTypeBefore->where("type_descuento_id", $type->id)->first();
                // validamos si existe algúna remuneracion
                if ($isType) {
                    $newDescuento = Descuento::updateOrCreate([
                        "work_id" => $info->work_id,
                        "info_id" => $info->id,
                        "planilla_id" => $isType->planilla_id,
                        "cargo_id" => $isType->cargo_id,
                        "categoria_id" => $isType->categoria_id,
                        "meta_id" => $isType->meta_id,
                        "cronograma_id" => $cronograma->id,
                        "type_descuento_id" => $type->id,
                        "base" => $type->base,
                        "mes" => $cronograma->mes,
                        "año" => $cronograma->año,
                        "adicional" => $cronograma->adicional
                    ]);
                    // actualizamos un monto
                    $newDescuento->update(["monto" => $isType->monto]);
                }
            }

        }else {

            $collecion = new InfoCollection($info);
            $collecion->updateOrCreateDescuento($cronograma, $this->types, $bases->where("info_id", $info->id));

        }

   }


}
