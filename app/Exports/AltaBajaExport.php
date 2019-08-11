<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;

use App\Models\Work;
use App\Models\Liquidar;

class AltaBajaExport implements FromView, ShouldQueue
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
        
        if ($this->mes > 0) {

            $inicio = "{$this->year}-{$this->mes}-01";
            $final = "{$this->year}-{$this->mes}-31";

        }else {

            $inicio = "{$this->year}-01-01";
            $final = "{$this->year}-12-31";

        }

        
        $altas = Work::where("fecha_de_ingreso", ">=", $inicio)
            ->where("fecha_de_ingreso", "<=", $final)
            ->get();

        $bajas = Liquidar::where("fecha_de_cese", ">=", $inicio)
            ->where("fecha_de_cese", "<=", $final)
            ->get();

            
        return view("exports.alta_baja", compact(
            'inicio', 'final', 'altas', 'bajas'
        ));

    }
}
