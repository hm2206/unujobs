<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    
    public function planilla()
    {
        return view("reports.planilla_mes");
    }

}
