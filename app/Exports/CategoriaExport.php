<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use App\Models\Categoria;

/**
 * Modelo de exportación de las Categorias
 */
class CategoriaExport implements FromView, ShouldQueue
{
    
    use Exportable;

    /**
     * Genera el archivo de exportación en excel
     *
     * @return View
     */
    public function view() : View
    {
        $categorias = Categoria::all();
        return view('exports.categoria', compact('categorias'));
    }

}
