<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Info;
use App\Models\Historial;

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
                "work_id" => $info->work_id,
                "info_id" => $info->id,
                "planilla_id" => $info->planilla_id,
                "cargo_id" => $info->cargo_id,
                "categoria_id" => $info->categoria_id,
                "meta_id" => $info->meta_id,
                "cronograma_id" => $this->cronograma->id,
                "fuente_id" => $info->fuente_id,
                "sindicato_id" => $info->sindicato_id,
                "afp_id" => $info->afp_id,
                "type_afp_id" => $info->type_afp_id,
                "numero_de_cussp" => $info->numero_de_cussp,
                "fecha_de_afiliacion" => $info->fecha_de_afiliacion,
                "banco_id" => $info->banco_id,
                "numero_de_cuenta" => $info->numero_de_cuenta,
                "numero_de_essalud" => $info->numero_de_essalud,
                "plaza" => $info->plaza,
                "perfil" => $info->perfil,
                "escuela" => $info->escuela,
                "pap" => $info->pap,
                "ext_pptto" => $info->cargo->ext_pptto,
                "observacion" => "---",
                "base" => 0,
                "base_enc" => 0,
                "total_bruto" => 0,
                "total_neto" => 0,
                "total_desct" => 0,
                "afecto" => $info->afecto,
                "orden" => $info->work ? substr($info->work->nombre_completo, 0, 2) : ""
            ]);
        }


        foreach (array_chunk($payload, 1000) as $body) {
            Historial::insert($body);
        }

    }
}
