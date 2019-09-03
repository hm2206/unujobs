<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;

use App\Models\Work;
use App\Models\TypeDescuento;
use App\Models\Descuento;

class AfpNet implements FromView, ShouldQueue
{

    use Exportable;

    private $cronograma;

    public function __construct($cronograma)
    {   
        $this->cronograma = $cronograma;
    }


    public function view() : View
    {

        $cronograma = $this->cronograma;
        $workIn = $cronograma->infos->pluck(["work_id"]);
        $works = Work::whereIn("id", $workIn)
            ->orderBy("nombre_completo", 'ASC')
            ->where("afp_id", "<>", null)
            ->get();

        $types = TypeDescuento::where("config_afp", "<>", null)->get();
        $descuentos = Descuento::where("cronograma_id", $cronograma->id)->get();
        
        foreach ($works as $work) {
            
            $whereIn = [];
            
            foreach ($types as $type) {
                
                $parse = json_decode($type->config_afp);
                
                if (is_array($parse) && count($parse) > 0) {

                    array_push($whereIn, $type->id);

                }

            }

            $work->tmp_afp = $descuentos->where("work_id")
                ->whereIn("type_descuento_id", $cronograma->id)
                ->sum("monto");

        }

        return view("exports.afpnet", \compact('works'));
    }
}
