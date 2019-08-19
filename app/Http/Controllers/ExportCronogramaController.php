<?php
/**
 * Http/Controllers/ExportCronogramaController.php
 * 
 * @author Hans <twd2206@gmail.com>
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\PlanillaExport;
use App\Models\TypeRemuneracion;
use App\Models\TypeCategoria;
use App\Models\TypeDescuento;
use App\Models\Cargo;
use App\Models\Cronograma;
use App\Models\Remuneracion;
use \PDF;
use App\Models\Meta;
use App\Jobs\GeneratePlanillaPDF;
use App\Jobs\GeneratePlanillaMetaPDF;
use App\Jobs\ReportCronograma;
use App\Jobs\ReportDescuento;
use App\Jobs\ReportBoleta;
use App\Jobs\ReportCuenta;
use App\Jobs\ReportCheque;
use App\Jobs\ExportQueue;
use App\Exports\AfpNet;
use \Carbon\Carbon;
use App\Models\Report;

/**
 * Class ExportCronogramaController
 * 
 * @category Controllers
 */
class ExportCronogramaController extends Controller
{
    /**
     * Crea un archivo de pdf del cronograma
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pdf(Request $request, $id)
    {
        $cronograma = Cronograma::findOrFail($id);

        try {
            
            $type = $request->type_report_id;
            GeneratePlanillaPDF::dispatch($cronograma, $type);

            return [
                "status" => true,
                "message" => "Este proceso durará unos minutos... Vuelva más tarde"
            ];

        } catch (\Throwable $th) {
           
            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación"
            ];

        }
        

    }


     /**
     * Crea un archivo de pdf del cronograma
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function meta(Request $request, $id)
    {
        $cronograma = Cronograma::findOrFail($id);

        try {
            
            $type = $request->type_report_id;
            GeneratePlanillaMetaPDF::dispatch($cronograma, $type);

            return [
                "status" => true,
                "message" => "Este proceso durará unos minutos... Vuelva más tarde"
            ];

        } catch (\Throwable $th) {
           
            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación"
            ];

        }
    }


     /**
     * Crea un archivo de pdf del cronograma
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function boleta(Request $request, $id)
    {
        $cronograma = Cronograma::findOrFail($id);

        try {
            
            $type = $request->type_report_id;
            ReportBoleta::dispatch($cronograma, $type);

            return [
                "status" => true,
                "message" => "Este proceso durará unos minutos... Vuelva más tarde"
            ];

        } catch (\Throwable $th) {
           
            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación"
            ];

        }
    }



    /**
     * Crea un archivo de pdf del cronograma
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pago(Request $request, $id)
    {
        $cronograma = Cronograma::findOrFail($id);

        try {
            
            $type = $request->type_report_id;
            $cuenta = $request->cuenta;
            $cheque = $request->cheque;

            if (!$cuenta && !$cheque) {
                abort(404);
            }

            if ($cuenta) {
                ReportCuenta::dispatch($cronograma, $type);
            }

            if ($cheque) {
                ReportCheque::dispatch($cronograma, $type);
            }

            return [
                "status" => true,
                "message" => "Este proceso durará unos minutos... Vuelva más tarde"
            ];

        } catch (\Throwable $th) {
           
            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación"
            ];

        }
    }


    public function afp(Request $request, $id)
    {
        try {
         
            $cronograma = Cronograma::findOrFail($id);
            $type_report = $request->type_report_id;

            //fecha
            $fecha = strtotime(Carbon::now());
            //ruta de apt-net
            $name_afp_net = "afp_net_{$fecha}.xlsx";
            $ruta_apt_net = "public/excels/{$name_afp_net}";

            //configurar archivos
            $config = [
                "type" => "excel",
                "name" => "Reporte en excel para el AFP NET",
                "icono" => "fas fa-file-excel",
                "path" => "/storage/excels/{$name_afp_net}",
                "cronograma_id" => $cronograma->id,
                "type_report" => $type_report
            ];

            // exportar afp-net
            (new AfpNet($cronograma))->queue($ruta_apt_net)->chain([
                new ExportQueue("/storage/excels/{$name_afp_net}", $name_afp_net, $config)
            ]);

            return [
                "status" => true,
                "message" => "Las solicitud está siendo procesada. Nosotros le notificaremos cuando este lista.",
                "body" => $cronograma
            ];
            
        } catch (\Throwable $th) {
            
            \Log::info($th);

            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación",
                "body" => ""
            ]; 

        }
       
    }


         /**
     * Crea un archivo de pdf del cronograma
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function planilla(Request $request, $id)
    {
        $cronograma = Cronograma::findOrFail($id);

        try {
            
            $type = $request->type_report_id;
            ReportCronograma::dispatch($cronograma, $type);

            return [
                "status" => true,
                "message" => "Este proceso durará unos minutos... Vuelva más tarde"
            ];

        } catch (\Throwable $th) {
           
            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación"
            ];

        }
    }
    

    /**
     * Crea un archivo de pdf del cronograma
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function descuento(Request $request, $id)
    {
        $cronograma = Cronograma::findOrFail($id);

        try {
            
            $type = $request->type_report_id;
            ReportDescuento::dispatch($cronograma, $type);

            return [
                "status" => true,
                "message" => "Este proceso durará unos minutos... Vuelva más tarde"
            ];

        } catch (\Throwable $th) {
           
            return [
                "status" => false,
                "message" => "Ocurrió un error al procesar la operación"
            ];

        }
    }
    /**
     * Crea un archivos pdf del cronograma
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reporte($mes, $year, $condicion)
    {
        return back()->with(["success" => "Este proceso durará unos minutos... Vuelva más tarde"]);
    }

}
