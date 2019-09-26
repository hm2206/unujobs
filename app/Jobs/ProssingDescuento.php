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
    private $types;

    public $timeout = 0;

    /**
     * @param object $cronograma
     * @param object $jobs
     * @return void
     */
    public function __construct($cronograma)
    {
        $this->cronograma = $cronograma;
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
        $this->infos = $cronograma->infos;
        // fecha anteriores
        $mes = $cronograma->mes == 1 ? 12 : $cronograma->mes - 1;
        $year = $cronograma->mes == 1 ? $cronograma->año - 1 : $cronograma->año; 
        // obtenemos remuneraciones anteriores
        $typeBefores = Descuento::whereIn("info_id", $this->infos->pluck(['id']))
            ->whereIn("type_descuento_id", $this->types->pluck(['id']))
            ->where("adicional", $cronograma->adicional)
            ->where("mes", $mes)
            ->where("año", $year)
            ->get();

        // obtener a los trabajadores con los descuentos del mes anterior
        $oldInfos = $this->infos->whereIn("id", $typeBefores->pluck(['info_id']));
        // obtener a los trabajadores recientemente agregados
        $newInfos = $this->infos->whereNotIn("id", $oldInfos->pluck(['id']));


        // configurar descuentos
        if ($oldInfos->count() > 0) {
            $this->configOldDescuento($cronograma, $oldInfos, $typeBefores);
        }

        //configurar descuentos nuevos
        if ($newInfos->count() > 0) {
            $this->configNewDescuento($cronograma, $newInfos);
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


    private function configOldDescuento($cronograma, $infos, $befores) 
    {

        $payload = [];

        foreach ($infos as $info) {

            foreach ($this->types as $type) {
                
                $typeBefore = $befores->where("type_descuento_id", $type->id)->first();
                $monto = isset($typeBefore->monto) ? $typeBefore->monto : 0;

                array_push($payload, [
                    "work_id" => $info->work_id,
                    "info_id" => $info->id,
                    "planilla_id" => $info->planilla_id,
                    "cargo_id" => $info->cargo_id,
                    "categoria_id" => $info->categoria_id,
                    "meta_id" => $info->meta_id,
                    "cronograma_id" => $cronograma->id,
                    "type_descuento_id" => $type->id,
                    "base" => $type->base,
                    "mes" => $cronograma->mes,
                    "año" => $cronograma->año,
                    "adicional" => $cronograma->adicional,
                    "monto" => $monto
                ]);

            }

        }

        foreach (array_chunk($payload, 1000) as $insert) {
            Descuento::insert($insert);
        }

    }


    private function configNewDescuento($cronograma, $infos)
    {
        $bases = Remuneracion::where("base", 0)
            ->where("cronograma_id", $cronograma->id)
            ->whereIn("info_id", $infos->pluck(['id']))
            ->get();

        foreach ($infos as $info) {
            $collecion = new InfoCollection($info);
            $collecion->updateOrCreateDescuento($cronograma, $this->types, $bases->where("info_id", $info->id));
        }
        
    }


}
