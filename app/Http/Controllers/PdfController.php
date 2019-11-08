<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Models\Meta;
use App\Models\Historial;
use App\Models\Cronograma;
use App\Models\TypeDescuento;
use App\Models\Aportacion;
use App\Tools\Helpers;
use App\Tools\Money;
use App\Models\Banco;
use App\Models\Afp;
use App\Models\TypeRemuneracion;
use App\Collections\BoletaCollection;
use App\Collections\GeneralCollection;
use App\Collections\PlanillaCollection;

class PdfController extends Controller
{
    
    /**
     * Reporte de boletas de pago x meta y planilla
     *
     * @param [type] $cronogramaID
     * @param [type] $metaID
     * @return void
     */
    public function boleta($cronogramaID, $metaID)
    {
        \set_time_limit(0);
        $cronograma = Cronograma::findOrFail($cronogramaID);
        // obtener meta
        $meta = Meta::findOrFail($metaID);
        // obtener historial
        $historial = Historial::with('work', 'cargo', 'categoria', 'meta')
            ->where('cronograma_id', $cronograma->id)
            ->orderBy('orden', 'ASC')
            ->where('meta_id', $meta->id)
            ->get();
        // obtener remuneraciones
        $remuneraciones = Remuneracion::with("typeRemuneracion")
            ->whereIn("historial_id", $historial->pluck(['id']))
            ->where("show", 1)
            ->get();
        // obtenemos  descuentos
        $descuentos = Descuento::with("typeDescuento")
        ->whereIn("historial_id", $historial->pluck(['id']))
        ->get(); 
        // generar configuracion para las boleta
        $boleta = BoletaCollection::init();
        $boleta->setRemuneraciones($remuneraciones);
        $boleta->setDescuentos($descuentos->where('base', 0));
        $boleta->setAportaciones($descuentos->where('base', 1));
        $boleta->setResource('http');
        $boleta->get($historial);
        return $boleta->view();
    }


    public function general_v1($cronogramaID) 
    {
        set_time_limit(0);
        $cronograma = Cronograma::findOrFail($cronogramaID);
        $general = new GeneralCollection($cronograma);
        // obtener historial x meta
        $historial = Historial::where('cronograma_id', $cronograma->id)->get();
        // obtener remuneraciones
        $remuneraciones = Remuneracion::whereIn('historial_id', $historial->pluck(['id']))->get();
        // obtener descuentos
        $descuentos = Descuento::whereIn('historial_id', $historial->pluck(['id']))->where('base', 0)->get();
        // obtener aportaciones
        $aportaciones = Aportacion::whereIn('historial_id', $historial->pluck(['id']))->get();
        // configurar reporte
        $general->setHistorial($historial);
        $general->setRemuneraciones($remuneraciones);
        $general->setDescunetos($descuentos);
        $general->setAportaciones($aportaciones);
        $general->generar("RESUMEN DE TODAS LAS METAS");
        return $general->render();
    }


    public function general_v2($cronogramaID) 
    {
        $cronograma = Cronograma::findOrFail($cronogramaID);
        $general = new GeneralCollection($cronograma);
        $general->generar();
        return $general->render();
    }


    /**
     * Reporte general v1 de meta x meta
     *
     * @param [type] $cronogramaID
     * @param [type] $metaID
     * @return void
     */
    public function meta_v1($cronogramaID, $metaID) 
    {
        set_time_limit(0);
        $cronograma = Cronograma::findOrFail($cronogramaID);
        $meta = Meta::findOrFail($metaID);
        $general = new GeneralCollection($cronograma);
        // obtener historial x meta
        $historial = Historial::where('meta_id', $meta->id)
            ->where('cronograma_id', $cronograma->id)
            ->get();
        // obtener remuneraciones
        $remuneraciones = Remuneracion::whereIn('historial_id', $historial->pluck(['id']))->get();
        // obtener descuentos
        $descuentos = Descuento::whereIn('historial_id', $historial->pluck(['id']))->where('base', 0)->get();
        // obtener aportaciones
        $aportaciones = Aportacion::whereIn('historial_id', $historial->pluck(['id']))->get();
        // configurar reporte
        $general->setHistorial($historial);
        $general->setRemuneraciones($remuneraciones);
        $general->setDescunetos($descuentos);
        $general->setAportaciones($aportaciones);
        $general->generar("META {$meta->metaID}");
        return $general->render();
    }


    /**
     * Muestra un reporte de todos los trabajadores de la planilla y meta especifica
     *
     * @param [type] $cronogramaID
     * @param [type] $metaID
     * @return void
     */
    public function planilla($cronogramaID, $metaID)
    {
        $cronograma = Cronograma::findOrFail($cronogramaID);
        $meta = Meta::findOrFail($metaID);
        $planilla = new PlanillaCollection($meta, $cronograma);
        $planilla->generate();
        return $planilla->render();
    }


    /**
     * Reporte de todos los descuentos
     *
     * @param [type] $cronogramaID
     * @return void
     */
    public function descuentos($cronogramaID)
    {
        $cronograma = Cronograma::findOrFail($cronogramaID);
        // obtener el historial de descuentos
        $historial = Historial::with('descuentos', 'work:works.id,nombre_completo,numero_de_documento')
            ->where('cronograma_id', $cronogramaID)
            ->get();
        // obtener mes
        $mes = Helpers::mes($cronograma->mes);
        // moneda
        $money = new Money;
        // totales
        $cronograma->total_bruto = 0;
        $cronograma->total_base = 0;
        $cronograma->total_desct = 0;
        $cronograma->total_neto = 0;
        // redenrizar
        return view('reports.descuentos', compact('historial', 'cronograma', 'mes', 'money'));
    }


    /**
     * Reporte de un descuento especifico
     *
     * @param [type] $cronogramaID
     * @param [type] $typeID
     * @return void
     */
    public function type_descuento($cronogramaID, $typeID)
    {
        set_time_limit(0);
        $cronograma = Cronograma::findOrFail($cronogramaID);
        $type = TypeDescuento::findOrFail($typeID);
        // obtener descuento del cronograma
        $descuentos = Descuento::with('work')
            ->where("type_descuento_id", $type->id)
            ->where('cronograma_id', $cronograma->id)
            ->where('monto', '>', 0)
            ->get();
        // obtener fecha del cronograma
        $mes = Helpers::mes($cronograma->mes);
        // moneda
        $money = new Money;
        // total
        $cronograma->total = 0;
        // redenrizar
        return view('reports.type_descuento', compact('descuentos', 'cronograma', 'mes', 'money', 'type'));
    }


    /**
     * Reporte de afp y seguros
     *
     * @return void
     */
    public function afp($cronogramaID, $afpID)
    {
        \set_time_limit(0);
        $cronograma = Cronograma::findOrFail($cronogramaID);
        $afp = Afp::findOrFail($afpID);
        $money = new Money;
        $mes = Helpers::mes($cronograma->mes);
        // obtenemos historial
        $historial = Historial::with('afp', 'work:works.id,nombre_completo')
            ->where("cronograma_id", $cronograma->id)
            ->where('afp_id', $afp->id)
            ->orderBy('orden', 'ASC')
            ->get();
        // obtenemos typos de descuentos
        $type_descuentos = TypeDescuento::whereHas('afp_primas')
            ->orWhereHas('afp_aportes')
            ->orWhereHas('type_afp')
            ->get();
        // obtener descuentos
        $descuentos = Descuento::whereIn('type_descuento_id', $type_descuentos->pluck(['id']))
            ->whereIn("historial_id", $historial->pluck(['id']))->get();
        // configurar historial
        foreach ($historial as $history) {
            // monto del aporte afp
            $history->pension = $descuentos->where("historial_id", $history->id)
                ->where('type_descuento_id', $type_descuentos->where('key', '32')->first()->id)
                ->sum('monto');
            // monto comision variable
            $history->ca = $descuentos->where("historial_id", $history->id)
                ->where('type_descuento_id', $type_descuentos->where('key', '34')->first()->id)
                ->sum('monto');
            // monto del prima de seguro
            $history->prima_seg = $descuentos->where("historial_id", $history->id)
                ->where('type_descuento_id', $type_descuentos->where('key', '35')->first()->id)
                ->sum('monto');
        }
        // crear totales cumulativos
        $cronograma->total_bruto = 0;
        $cronograma->pension = 0;
        $cronograma->ca = 0;
        $cronograma->prima_seg = 0;
        $cronograma->total_desct = 0;

        return view('reports.afp', compact('afp', 'historial', 'cronograma', 'mes', 'money'));
    }


    /**
     * Reporte de personal x cronograma
     *
     * @param [type] $cronogramaID
     * @return void
     */
    public function relacion_personal($id)
    {
        $negativo = request()->input('negativo', 0);
        $cronograma = Cronograma::findOrFail($id);
        // config
        $mes = Helpers::mes($cronograma->mes);
        $money = new Money;
        $cronograma->total_bruto = 0;
        $cronograma->total_neto = 0;
        // obtener historial
        $historial = Historial::with('categoria', 'work:works.id,nombre_completo,numero_de_documento')
            ->where('cronograma_id', $cronograma->id);
        // verificamos si los saldos son pasitivo o negativos
        $historial = $negativo 
            ? $historial->where('total_neto', '<=', 0) 
            : $historial->where('total_neto', '>', 0);
        // recuperamos historial
        $historial = $historial->get();
        return view('reports.relacion_personal', compact('negativo', 'cronograma', 'historial', 'money', 'mes'));
    }


    public function pago($id)
    {
        $cuenta = request()->input('cuenta', 0);
        $cronograma = Cronograma::findOrFail($id);
        $money = new Money;
        $mes = Helpers::mes($cronograma->mes);
        $totales = 0;
        $beforeTotal = 0;
        $beforeBon = [];
        // obtener tipo de bonificaciones
        $bonificaciones = TypeRemuneracion::where("bonificacion", 1)->get();
        // obtener historial
        $historial = $cronograma->historial()
            ->with('work:works.id,nombre_completo,numero_de_documento')
            ->orderBy('orden', 'ASC');
        // verificar si es con cuenta o con cheque
        $historial = $cuenta 
            ? $historial->where("numero_de_cuenta", '<>', '') 
            : $historial->where("numero_de_cuenta", '');
        // recuperar historial
        $historial = $historial->get();
        // obtener remuneraciones
        $remuneraciones = Remuneracion::where("show", 1)
            ->whereIn("historial_id", $historial->pluck(['id']))
            ->whereIn("type_remuneracion_id", $bonificaciones->pluck(['id']))
            ->get();
        // vista
        $view = $cuenta ? "reports.cuenta" : "reports.cheque";
        return view($view, compact(
            'cronograma', 'historial', 'mes', 'money', 'totales', 'bonificaciones',
            'remuneraciones', 'beforeTotal', 'beforeBon'
        ));
    }


    public function cheque($id)
    {
        $cronograma = Cronograma::findOrFail($id);
    }
}
