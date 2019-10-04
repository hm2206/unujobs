<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \PDF;
use App\Models\Cronograma;
use App\Jobs\EjecucionJob;
use App\Jobs\EjecucionDetalleJob;
use App\Models\User;

class RptController extends Controller
{
    
    public function descuentoBruto(Request $request, $id) {
        $this->validate(request(), [
            "type_report_id" => "required"
        ]);

        try {
            $cronograma = Cronograma::findOrFail($id);
            $type = $request->type_report_id;
            EjecucionJob::dispatch($cronograma, $type, 0)->onQueue("medium");
            
            return [
                "status" => true,
                "message" => "El reporte está siendo procesado, nosotros le notificaremos"
            ];
        } catch (\Throwable $th) {

            \Log::info($th);
            
            return [
                "status" => false,
                "message" => "Ocurrio un error al procesar la operación!"
            ];
        }
    }


    public function descuentoBrutoDetalle(Request $request, $id) {
        $this->validate(request(), [
            "type_report_id" => "required"
        ]);

        try {
            $cronograma = Cronograma::findOrFail($id);
            $type = $request->type_report_id;
            EjecucionDetalleJob::dispatch($cronograma, $type, 0)->onQueue("medium");
            
            return [
                "status" => true,
                "message" => "El reporte está siendo procesado, nosotros le notificaremos"
            ];
        } catch (\Throwable $th) {

            \Log::info($th);
            
            return [
                "status" => false,
                "message" => "Ocurrio un error al procesar la operación!"
            ];
        }
    }


    public function descuentoNeto(Request $request, $id) {
        $this->validate(request(), [
            "type_report_id" => "required"
        ]);

        try {
            $cronograma = Cronograma::findOrFail($id);
            $type = $request->type_report_id;
            EjecucionJob::dispatch($cronograma, $type, 1)->onQueue("medium");
            
            return [
                "status" => true,
                "message" => "El reporte está siendo procesado, nosotros le notificaremos"
            ];
        } catch (\Throwable $th) {

            \Log::info($th);
            
            return [
                "status" => false,
                "message" => "Ocurrio un error al procesar la operación!"
            ];
        }
    }


    public function descuentoNetoDetalle(Request $request, $id) {
        $this->validate(request(), [
            "type_report_id" => "required"
        ]);

        try {
            $cronograma = Cronograma::findOrFail($id);
            $type = $request->type_report_id;
            EjecucionDetalleJob::dispatch($cronograma, $type, 1)->onQueue("medium");
            
            return [
                "status" => true,
                "message" => "El reporte está siendo procesado, nosotros le notificaremos"
            ];
        } catch (\Throwable $th) {

            \Log::info($th);
            
            return [
                "status" => false,
                "message" => "Ocurrio un error al procesar la operación!"
            ];
        }
    }

}
