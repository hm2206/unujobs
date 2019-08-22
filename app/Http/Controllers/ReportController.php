<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Report;

class ReportController extends Controller
{
    
    public function files($id, $type) 
    {
        return Report::orderBy("id", "DESC")
            ->where("cronograma_id", $id)
            ->where("type_report_id", $type)
            ->get();
    }


    public function markAsRead($id)
    {
        $report = Report::findOrFail($id);
        $report->update(["read" => 1]);
        return $report;
    }

}
