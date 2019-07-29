<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use App\Models\Categoria;

class CategoriaExport implements FromView, ShouldQueue
{
    
    use Exportable;

    public function view() : View
    {
        $categorias = Categoria::all();
        return view('exports.categoria', compact('categorias'));
    }

}
