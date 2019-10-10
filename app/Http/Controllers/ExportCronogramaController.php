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
use App\Jobs\ReportGeneral;
use App\Jobs\GeneratePlanillaMetaPDF;
use App\Jobs\ReportGeneralMeta;
use App\Jobs\ReportCronograma;
use App\Jobs\ReportDescuento;
use App\Jobs\ReportDescuentoType;
use App\Jobs\ReportDescuentoTypeMulti;
use App\Jobs\ReportBoleta;
use App\Jobs\ReportCuenta;
use App\Jobs\ReportCheque;
use App\Jobs\ExportQueue;
use App\Exports\AfpNet;
use \Carbon\Carbon;
use App\Models\Report;
use App\Jobs\ReportPersonalCronograma;

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

        /*if ($cronograma->estado > 0) {
            return [
                "status" => false,
                "message" => "La planilla aún está en curso, espere el cierre!"
            ];
        }*/

        try {
            
            $type = $request->type_report_id;
            GeneratePlanillaPDF::dispatch($cronograma, $type)->onQueue("medium");
            ReportGeneral::dispatch($cronograma, $type)->onQueue("medium");

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

        /*if ($cronograma->estado > 0) {
            return [
                "status" => false,
                "message" => "La planilla aún está en curso, espere el cierre!"
            ];
        }*/

        try {
            
            $type = $request->type_report_id;
            $meta_id = $request->meta_id;

            if ($meta_id) {
                GeneratePlanillaMetaPDF::dispatch($cronograma, $type, $meta_id)->onQueue("medium");
                ReportGeneralMeta::dispatch($cronograma, $type, $meta_id)->onQueue("medium");
            }

            return [
                "status" => true,
                "message" => "Este proceso durará unos minutos... Vuelva más tarde"
            ];

        } catch (\Throwable $th) {
           
            \Log::info($th);
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

        if ($cronograma->estado > 0) {
            return [
                "status" => false,
                "message" => "La planilla aún está en curso, espere el cierre!"
            ];
        }

        try {
            
            $type = $request->type_report_id;
            $meta_id = $request->meta_id;
            
            if ($meta_id) {
                ReportBoleta::dispatch($cronograma, $type, $meta_id)->onQueue('medium');
            }else {
                abort(401);
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
                ReportCuenta::dispatch($cronograma, $type)->onQueue('low');
            }

            if ($cheque) {
                ReportCheque::dispatch($cronograma, $type)->onQueue('low');
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
                (new ExportQueue("/storage/excels/{$name_afp_net}", $name_afp_net, $config))
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

        if ($cronograma->estado > 0) {
            return [
                "status" => false,
                "message" => "La planilla aún está en curso, espere el cierre!"
            ];
        }

        try {
            
            $type = $request->type_report_id;
            $meta_id = $request->meta_id;

            if ($meta_id) {
                $infoIn = $cronograma->infos->where("meta_id", $meta_id)->pluck(["id"]);
                ReportCronograma::dispatch($cronograma, $type, $infoIn, $meta_id)->onQueue('medium');
            }else {
                abort(401);
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
            $type_descuento = $request->input('type_descuento', "");
            $detalle = $request->detalle;

            if ($type_descuento) {
                if ($detalle) {
                    ReportDescuentoTypeMulti::dispatch($cronograma, $type, $type_descuento)->onQueue('medium');
                }else {
                    ReportDescuentoType::dispatch($cronograma, $type, $type_descuento)->onQueue('medium');
                }
            }else {
                ReportDescuento::dispatch($cronograma, $type)->onQueue('medium');
            }


            return [
                "status" => true,
                "message" => "Este proceso durará unos minutos... Vuelva más tarde"
            ];

        } catch (\Throwable $th) {
            
            \Log::info($th);

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
    public function personal(Request $request, $id)
    {
        $cronograma = Cronograma::findOrFail($id);

        try {
            
            $type = $request->type_report;
            $condicion = $request->condicion;

            ReportPersonalCronograma::dispatch($cronograma, $condicion, $type)->onQueue('medium');

            return [
                "status" => true,
                "message" => "Este proceso durará unos minutos... Vuelva más tarde"
            ];

        } catch (\Throwable $th) {
            
            \Log::info($th);

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
