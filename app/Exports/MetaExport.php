<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use App\Models\Meta;

class MetaExport implements FromView, ShouldQueue
{

    use Exportable;
    
    private $limite = 0;
    private $order = 'ASC';


    public function __construct($limite = 0, $order = 'ASC')
    {
        $this->limite = $limite;
        $this->order = $order;
    }


    public function view() : View
    {
        $metas = Meta::orderBy('id', $this->order)
            ->take($this->limite)
            ->get();

        return view('exports.meta', compact('metas'));
    }

}
