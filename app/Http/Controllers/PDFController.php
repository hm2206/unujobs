<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Collections\CronogramaCollection;
use App\Models\Cronograma;
use App\Models\Work;
use \DB;
use \PDF;

class PDFController extends Controller
{
    
    public function boleta($id)
    {
        $cronograma = Cronograma::findOrFail($id);
        $collect = new CronogramaCollection($cronograma);
        $boleta = $collect->boleta();

        return $boleta;

    }

}
