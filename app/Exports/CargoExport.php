<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use App\Models\Cargo;

/**
 * Modelo de exportación de los cargos
 */
class CargoExport implements FromView, ShouldQueue
{
    use Exportable;

    /**
     * Genera el archivo de exportación en excel
     *
     * @return View
     */
    public function view() : View
    {
        $cargos = Cargo::all();
        return view('exports.cargo', compact('cargos'));
    }

}
