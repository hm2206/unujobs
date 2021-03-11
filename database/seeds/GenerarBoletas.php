<?php

use Illuminate\Database\Seeder;

use App\Models\Historial;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Collections\BoletaCollection;
use Illuminate\Support\Facades\Storage;

class GenerarBoletas extends Seeder
{

    public $timeout = 0;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // historial
        $historial = Historial::with('work', 'cronograma')->get();
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
            $boleta->setYear($history->cronograma->año);
            $boleta->setMes($history->cronograma->mes);
            $boleta->find($history);
            $pdf = $boleta->pdf();
            $pdf->setPaper('a4', 'landscape');
            // ruta 
            $path = "pdf/boletas/{$history->cronograma->año}_{$history->cronograma->mes}/{$nombre}";
            // guardar boleta en larua
            Storage::disk('public')->put($path, $pdf->output());
            // guardar boleta
            $history->update([ "pdf" => "/storage/{$path}" ]);
        }
    }
}
