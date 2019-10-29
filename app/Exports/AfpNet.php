<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;

use App\Models\Work;
use App\Models\TypeDescuento;
use App\Models\Descuento;
use App\Models\Historial;
use App\Models\Afp;

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
        // obtenemos las Afps Activadas
        $afps = Afp::where('activo', 1)->get();
        // obtenemos el historial de trabajadores
        $historial = Historial::whereHas('afp', function($a) use($afps) {
                $a->whereIn("afp_id", $afps->pluck(['id']));
            })->with('work')
            ->where('cronograma_id', $cronograma->id)
            ->get();
        // obtener los tipos de descuentos que se afectan en la afp
        $types = TypeDescuento::whereHas('afp_primas')
            ->orWhereHas('afp_aportes')
            ->orWhereHas('type_afp')
            ->get();

        return view("exports.afpnet", \compact('historial'));
    }
}
