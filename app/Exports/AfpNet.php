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

        $works = Work::whereHas("afp")->where("numero_de_cussp", "<>", null)->get();
        
        foreach ($works as $work) {
            
            $whereIn = [];
            $types = TypeDescuento::where("config_afp", "<>", null)->get();

            foreach ($types as $type) {
                
                $parse = json_decode($type->config_afp);
                
                if (is_array($parse) && count($parse) > 0) {

                    array_push($whereIn, $type->id);

                }

            }

            $work->tmp_afp = Descuento::where("work_id", $work->id)
                ->where("cronograma_id", $this->cronograma->id)
                ->whereIn("type_descuento_id", $whereIn)
                ->get()
                ->sum("monto");

        }

        return view("exports.afpnet", \compact('works'));
    }
}
