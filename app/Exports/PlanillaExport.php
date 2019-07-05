<?php

namespace App\Exports;

use App\Models\Work;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class PlanillaExport implements FromView
{
  
    public function view(): View
    {
        $works = Work::all();
        return view('exports.cronograma', \compact('works'));
    }

}
