<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use App\Models\Cronograma;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Models\Planilla;
use App\Models\TypeRemuneracion;
use App\Models\TypeDescuento;
use App\Models\Work;

/**
 * Modelo de exportación del cronograma
 */
class CronogramaExport implements FromView, ShouldQueue
{
    use Exportable;
    
    private $id; 

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }


    /**
     * Genera el archivo de exportación en excel
     *
     * @return View
     */
    public function view() : View
    {
        $cronograma = Cronograma::findOrFail($this->id);
        $planilla = Planilla::findOrFail($cronograma->planilla_id);
        $workIn = Remuneracion::where("cronograma_id", $cronograma->id)->get()->pluck(["work_id"]);
        $works = Work::whereIn("id", $workIn)->get();

        $type_remuneraciones = TypeRemuneracion::orderBy('id', 'ASC')->get();
        $type_descuentos = TypeDescuento::all();

        foreach ($works as $work) {
            
            $infos = $work->infos->where("planilla_id", $planilla->id);
            $work->infos = $infos;

            foreach ($work->infos as $info) {
            
                $info->remuneraciones = Remuneracion::orderBy('type_remuneracion_id', 'ASC')
                    ->where('work_id', $work->id)
                    ->where("categoria_id", $info->categoria_id)
                    ->where("cargo_id", $info->cargo_id)
                    ->where("cronograma_id", $cronograma->id)
                    ->where("planilla_id", $info->planilla_id)
                    ->get();

                $info->total_bruto = $info->remuneraciones->sum('monto');

                $info->descuentos = Descuento::where('work_id', $work->id)
                    ->where("categoria_id", $info->categoria_id)
                    ->where("cargo_id", $info->cargo_id)
                    ->where("cronograma_id", $cronograma->id)
                    ->where("planilla_id", $info->planilla_id)
                    ->get();

                $info->total_descuento = $info->descuentos->where('base', 0)->sum('monto');
                $info->base = $info->remuneraciones->where('base', 0)->sum('monto');
                $info->total_neto = $info->total_bruto - $info->total_descuento;

            }

        }

        return view('exports.cronograma', compact(
            'cronograma', 'works', 'planilla', 'type_remuneraciones', 'type_descuentos'
        ));
    }

}
