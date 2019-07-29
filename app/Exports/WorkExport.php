<?php

namespace App\Exports;

use App\Models\Work;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;

class WorkExport implements FromView, ShouldQueue
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
        $works = Work::orderBy('id', $this->order)
            ->take($this->limite)
            ->get();

        return view('exports.work', compact('works'));
    }

}
