<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Info;
use App\Models\Boleta;

class AddInfoCronograma implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $cronograma;
    private $infoIn = [];
    private $infos;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($cronograma, array $infoIn = [])
    {
        $this->cronograma = $cronograma;
        $this->infoIn = $infoIn;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // almacen para realizar la transacción a la db
        $payload = [];
        // trae a todos los infos que pertenecen a la planilla
        $this->infos = Info::with('cargo', 'work')
            ->whereHas("work", function($w) {
                $w->orderBy("nombre_completo", "ASC");
            })->where("planilla_id", $this->cronograma->planilla_id)
                ->where("active", 1);
        // preguntar si solo se traerán datos por ids personalizados
        if (count($this->infoIn) > 0) {
            $this->infos = $this->infos->whereIn("id", $this->infoIn);
        }
        // obtener infos filtrados
        $this->infos = $this->infos->get();
        // canfigurar infos para realizar la guardar en la base de datos
        foreach ($this->infos as $info) {
            array_push($payload, [
                "cronograma_id" => $this->cronograma->id,
                "info_id" => $info->id,
                "observacion" => $info->observacion,
                "meta_id" => $info->meta_id,
                "pap" => $info->pap,
                "ext_pptto" => $info->cargo->ext_pptto,
                "afp_id" => $info->work->afp_id,
                "perfil" => $info->perfil,
                "planilla_id" => $info->planilla_id,
                "cargo_id" => $info->cargo_id,
                "categoria_id" => $info->categoria_id,
                "numero_de_cussp" => $info->work->numero_de_cussp,
                "numero_de_essalud" => $info->work->numero_de_essalud,
                "meta_id" => $info->meta_id,
                "work_id" => $info->work_id,
                "plaza" => $info->plaza,
                "escuela" => $info->escuela,
                "sindicato_id" => $info->sindicato_id
            ]);
        }


        foreach (array_chunk($payload, 1000) as $body) {
            Boleta::insert($body);
        }

    }
}
