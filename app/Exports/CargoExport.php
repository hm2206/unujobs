<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use App\Models\Cargo;

class CargoExport implements FromView, ShouldQueue
{
    use Exportable;


    public function view() : View
    {
        $cargos = Cargo::all();
        return view('exports.cargo', compact('cargos'));
    }

}
