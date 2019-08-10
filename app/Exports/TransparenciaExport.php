<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;

use App\Models\Work;
use App\Models\Remuneracion;
use App\Models\Liquidar;

class TransparenciaExport implements FromView, ShouldQueue
{
    
    use Exportable;

    private $mes;
    private $year;

    public function __construct($year, $mes)
    {
        $this->mes = $mes;
        $this->year = $year;
    }

    public function view() : View
    {
        
        $count_bajas = Liquidar::where("mes", $this->mes)
            ->where("año", $this->year)
            ->get()
            ->count();

        $salario_min = Remuneracion::where("mes", $this->mes)
            ->where("año", $this->year)
            ->groupBy("work_id")
            ->min("monto");

        $salario_max = Remuneracion::where("mes", $this->mes)
            ->where("año", $this->year)
            ->groupBy("work_id")
            ->max("monto");

        $bonificaciones = Remuneracion::whereHas('typeRemuneracion', function($q) {
            $q->where("bonificacion", 1);
        })->get()
        ->count();

        $count_personal = Work::get()->count();  

        $mes = $this->mes;
        $year = $this->year;

        return view("exports.transparencia", compact(
            'count_bajas', 'salario_min', 'salario_max', 
            'bonificaciones', 'count_personal', 'mes', 'year'
        ));

    }


}
