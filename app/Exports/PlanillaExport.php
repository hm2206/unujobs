<?php

namespace App\Exports;

use App\Models\Work;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;

class PlanillaExport implements FromCollection
{

    private $limite = 0;
    private $order = 'ASC';


    public function __construct($limite = 0, $order = 'ASC')
    {
        $this->limite = $limite;
        $this->order = $order;
    }

  
    public function collection()
    {
        return Work::orderBy('id', $this->order)
            ->take()
            ->get();
    }

}
