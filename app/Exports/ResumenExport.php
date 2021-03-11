<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;

use App\Models\Work;
use App\Models\Remuneracion;


class ResumenExport implements FromView, ShouldQueue
{
    use Exportable;


    private $year;
    private $mes;


    public function __construct($year, $mes)
    {
        $this->year = $year;
        $this->mes = $mes;
    }


    public function view() : View
    {
    
        $works = Work::all();

        foreach ($works as $work) {
            
            $monto_bruto = Remuneracion::where("work_id", $work->id);

            if ($this->mes > 0) {

                $monto_bruto = $monto_bruto->where("mes", $this->mes)
                    ->where("año", $this->year)
                    ->sum("monto");
    
            }else {
                
                $monto_bruto = $monto_bruto->where("año", $this->year)
                    ->sum("monto");
    
            }

            $work->monto_bruto = $monto_bruto;

        }


            
        return view("exports.resumen", compact(
            'works'
        ));

    }

}
