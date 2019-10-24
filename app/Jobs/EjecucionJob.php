<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\TypeCategoria;
use App\Models\Descuento;
use App\Models\Remuneracion;
use App\Models\TypeRemuneracion;
use App\Models\Meta;
use App\Tools\Money;
use App\Models\Report;
use \PDF;
use App\Models\User;
use App\Models\Historial;
use App\Notifications\ReportNotification;

class EjecucionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;
    private $neto = 0;
    private $cronograma;
    private $type_report;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($cronograma, $type_report, $neto)
    {
        $this->cronograma = $cronograma;
        $this->neto = $neto;
        $this->type_report = $type_report;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $contenidos = [];
        $footer = [];
        $cronograma = $this->cronograma;

        $meses = [
            "Enero", "Febrero", "Marzo", "Abril", 
            "Mayo", 'Junio', 'Julio', 'Agosto', 
            'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];

        $allRemuneraciones = Remuneracion::where("show", 1)->where("cronograma_id", $cronograma->id)->get();
        $typeBonificaciones = TypeRemuneracion::where("bonificacion", 1)->get();
        $descuentos = [];

        $bonificaciones = $allRemuneraciones->whereIn("type_remuneracion_id", $typeBonificaciones->pluck(['id']));
        $remuneraciones = $allRemuneraciones->whereNotIn("type_remuneracion_id", $typeBonificaciones->pluck(['id']));

        if ($this->neto) {
            $descuentos = Descuento::where("cronograma_id", $cronograma->id)->get();
        }

        $metas = Meta::whereIn("id", $allRemuneraciones->pluck('meta_id'))->get();
        
        $type_categorias = TypeCategoria::whereHas('cargos', function($c) use($cronograma) {
                $c->where("planilla_id", $cronograma->planilla_id);
            })->with('cargos')->get();

        $contenidos = [
            "metas" => ["size" => "20%", "content" => $metas->pluck(['meta'])],
            "actividad" => ["size" => '7%', "content" => $metas->pluck(['actividadID'])],
            "meta" => ["size" => '4%', 'content' => $metas->pluck(['metaID'])],
        ];

        // recorrer los montos por cargo
        foreach ($type_categorias as $categoria) {
            foreach ($categoria->cargos as $cargo) {
                
                $payload = [];
                $total = 0;
                $size = (100 * 1 ) / $categoria->cargos->count();

                foreach ($metas as $meta) {
                    $monto = $remuneraciones->where("cargo_id", $cargo->id)
                        ->where("meta_id", $meta->id)
                        ->sum("monto");

                    if ($this->neto) {
                        $monto = $monto - $descuentos->where("base", 0)
                            ->where("cargo_id", $cargo->id)
                            ->where("meta_id", $meta->id)
                            ->sum("monto");
                    }

                    $total += $monto;
                    array_push($payload,  Money::parse($monto));
                }

                $key = str_replace(" ", "-", strtolower($cargo->descripcion));
                $contenidos[$key] = ["size" => $size, "content" => $payload];
                array_push($footer, Money::parse($total));
                
            }
        }

        // recorrer las bonificaciones
        foreach ($typeBonificaciones as $bonificacion) {
            $payload = [];
            $total = 0;
            $size = (100 * 1 ) / $typeBonificaciones->count();

            foreach ($metas as $meta) {
                $monto = $bonificaciones->where("meta_id", $meta->id)->sum("monto");
                $total += $monto;
                array_push($payload, Money::parse($monto));
            }

            $contenidos[$bonificacion->key] =  ["size" => "{$size}%","content" => $payload];
            array_push($footer, Money::parse($total));
        }

        // totales
        $payload = [];
        $total = 0;
        foreach ($metas as $meta) {
            $monto = $allRemuneraciones->where("meta_id", $meta->id)->sum("monto");

            if ($this->neto) {
                $monto = $monto - $descuentos->where("base", 0)
                    ->where("meta_id", $meta->id)
                    ->sum("monto");
            }

            $total += $monto;
            array_push($payload, Money::parse($monto));
        }

        $contenidos["totales"] = ["size" => "10%", "content" => $payload];
        array_push($footer, Money::parse($total));
        $neto = $this->neto;

        $pdf = \PDF::loadView('reportes.descuentoBruto', compact('cronograma','meses', 'type_categorias', 'contenidos', 'neto', 'footer'));
        $pdf->setPaper('a3', 'landscape')->setWarnings(false);

        $message = $this->neto ? 'Neto' : 'Bruto';
        $path = "pdf/resumen_ejecucion_{$message}_{$cronograma->mes}_{$cronograma->año}_{$cronograma->id}.pdf";
        $planilla = $cronograma->planilla ? $cronograma->planilla->descripcion : '';
        $nombre = "Descuento {$message} {$planilla}";

        $pdf->save(storage_path("app/public") . "/{$path}");
        $archivo = Report::where("cronograma_id", $cronograma->id)
            ->where("type_report_id", $this->type_report)
            ->where("name", $nombre)
            ->first();

        if ($archivo) {
            $archivo->update([
                "read" => 0,
                "path" => "/storage/{$path}"
            ]);
        }else {
            $archivo = Report::create([
                "type" => "pdf",
                "name" => $nombre,
                "icono" => "fas fa-file-pdf",
                "path" => "/storage/{$path}",
                "cronograma_id" => $cronograma->id,
                "type_report_id" => $this->type_report
            ]);
        }

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification($archivo->path ,"{$archivo->name}, ya está lista"));
        }
        
    }
}
