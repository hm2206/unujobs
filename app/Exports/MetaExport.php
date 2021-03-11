<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use App\Models\Meta;

/**
 * Modelo de exportación de las Metas presupuestales
 */
class MetaExport implements FromView, ShouldQueue
{

    use Exportable;
    
    /**
     * @var integer
     */
    private $limite = 0;


    /**
     * @var string
     */
    private $order = 'ASC';


    /**
     * @param integer $limite
     * @param string $order
     */
    public function __construct($limite = 0, $order = 'ASC')
    {
        $this->limite = $limite;
        $this->order = $order;
    }


    /**
     * Genera el archivo de exportación en excel
     *
     * @return View
     */
    public function view() : View
    {
        $metas = Meta::orderBy('id', $this->order)
            ->take($this->limite)
            ->get();

        return view('exports.meta', compact('metas'));
    }

}
