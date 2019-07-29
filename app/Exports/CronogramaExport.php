<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use App\Models\Cronograma;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Models\TypeRemuneracion;
use App\Models\TypeDescuento;
use App\Models\Work;

class CronogramaExport implements FromView, ShouldQueue
{
    use Exportable;
    
    private $id; 

    public function __construct($id)
    {
        $this->id = $id;
    }


    public function view() : View
    {
        $cronograma = Cronograma::findOrFail($this->id);
        $workIn = Remuneracion::where("cronograma_id", $cronograma->id)->get()->pluck(["work_id"]);
        $works = Work::whereIn("id", $workIn)->get();

        $type_remuneraciones = TypeRemuneracion::all();
        $type_descuentos = TypeDescuento::all();

        foreach ($works as $work) {
            
            foreach ($work->infos as $info) {
            
                $info->remuneraciones = Remuneracion::where('work_id', $work->id)
                    ->where("categoria_id", $info->categoria_id)
                    ->where("cargo_id", $info->cargo_id)
                    ->where("cronograma_id", $cronograma->id)
                    ->where("planilla_id", $info->planilla_id)
                    ->get();

                $info->descuentos = Descuento::where('work_id', $work->id)
                    ->where("categoria_id", $info->categoria_id)
                    ->where("cargo_id", $info->cargo_id)
                    ->where("cronograma_id", $cronograma->id)
                    ->where("planilla_id", $info->planilla_id)
                    ->get();

            }

        }

        return view('exports.cronograma', compact('cronograma', 'works'));
    }

}
