<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\Historial;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Models\Cronograma;
use App\Collections\BoletaCollection;
use Illuminate\Support\Facades\Storage;

class TurnOffPlanilla implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;
    private $cronograma;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($cronograma)
    {
        $this->cronograma = $cronograma;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // historial
        $historial = Historial::with('work')->where('cronograma_id', $this->cronograma->id)->get();
        // obtener remuneraciones
        $remuneraciones = Remuneracion::whereIn('historial_id', $historial->pluck(['id']))
            ->where('show', 1)->get();
        // obtener descuentos
        $descuentos = Descuento::where('base', 0)->whereIn('historial_id', $historial->pluck(['id']))->get();
        // obtener aportaciones
        $aportaciones = Descuento::where('base', 1)->whereIn('historial_id', $historial->pluck(['id']))->get();
        // generar boletas
        foreach ($historial as $history) {
            // crear nombre del pdf
            $nombre = str_replace(" ", "_", $history->work->nombre_completo) . "_{$history->id}.pdf";
            $boleta = BoletaCollection::init();
            $boleta->setRemuneraciones($remuneraciones);
            $boleta->setDescuentos($descuentos);
            $boleta->setAportaciones($aportaciones);
            $boleta->setYear($this->cronograma->año);
            $boleta->setMes($this->cronograma->mes);
            $boleta->find($history);
            $pdf = $boleta->pdf();
            $pdf->setPaper('a4', 'landscape');
            // ruta 
            $path = "pdf/boletas/{$this->cronograma->año}_{$this->cronograma->mes}/{$nombre}";
            // recurso para email
            $output = $pdf->output();
            // guardar boleta en larua
            Storage::disk('public')->put($path, $output);
            // guardar boleta
            $history->update([
                "boleta" => $output, 
                "pdf" => "/storage/{$path}" 
            ]);
        }
    }
}
