<?php

namespace App\Collections;

use App\Models\Historial;
use App\Tools\Money;
use App\Models\Descuento;
use App\Models\Remuneracion;
use App\Models\TypeDescuento;
use App\Models\TypeRemuneracion;
use App\Models\TypeAfp;
use App\Models\Afp;
use App\Models\TypeAportacion;
use App\Models\Cronograma;
use App\Models\TypeCategoria;
use App\Models\Meta;
use App\Tools\Helpers;
use \PDF;

class GeneralCollection
{

    private $cronograma;
    private $storage;
    private $meta;
    private $historial;
    private $type_categorias;
    private $type_remuneraciones;
    private $type_descuentos;
    private $type_aportaciones;
    private $afps;
    private $type_afps;
    private $remuneraciones;
    private $descuentos;
    private $aportaciones;
    private $view = 'reports.general';

    public function __construct(Cronograma $cronograma)
    {   
        $this->cronograma = $cronograma;
        $this->afps = Afp::orderBy("nombre", "ASC")->where('activo', 1)->get();
        $type_afps = TypeAfp::whereHas("type_descuento")->get();
    }


    public function setRemuneraciones($remuneraciones)
    {
        $this->remuneraciones = $remuneraciones;
    }


    public function setDescunetos($descuentos)
    {
        $this->descuentos = $descuentos;
    }


    public function setAportaciones($aportaciones)
    {
        $this->aportaciones = $aportaciones;
    }


    public function setMeta(Meta $meta)
    {
        $this->meta = $meta;
    }


    public function setHistorial($historial)
    {
        $this->historial = $historial;
    }


    public function getStorage()
    {
        return $this->storage;
    }


    public function generar($titulo = "")
    {
        // configurar categorias type
        $config = [
            "planilla_id" => $this->cronograma->planilla_id,
            "cargo_id" => $this->historial->pluck(['cargo_id'])
        ];
        $this->type_categorias = TypeCategoria::with('cargos')
            ->whereHas('cargos', function($car) use($config) {
                $car->where("cargos.planilla_id", $config['planilla_id'])
                    ->whereIn('cargos.id', $config['cargo_id']);
            })->get();
        // configuracion
        $this->configRemuneraciones($this->remuneraciones);
        $this->configDescuentos($this->descuentos);
        $this->configAfps();
        $this->configAportaciones($this->aportaciones);

        // variables
        $money = new Money;
        $cronograma = $this->cronograma;
        $historial = $this->historial;
        $type_remuneraciones = $this->type_remuneraciones;
        $type_descuentos = $this->type_descuentos;
        $type_categorias = $this->type_categorias;
        $type_aportaciones = $this->type_aportaciones;
        $afps = $this->afps;
        $remuneraciones = $this->remuneraciones;
        $mes = Helpers::mes($cronograma->mes);

        // configuracion de los totales
        $cronograma->total_bruto = $historial->sum("total_bruto");
        // total de afps
        $cronograma->afp_total = $afps->sum('monto');
        // total de los descuentos
        $cronograma->total_descuentos = $historial->sum('total_desct');
        // obtenemos el total neto de la planilla
        $cronograma->total_liquido = $historial->sum('total_neto');

        
        // retornar datos necesarios para el cronograma
        $this->storage = \compact(
            'type_remuneraciones', 'mes', 'cronograma', 
            'type_categorias', 'type_descuentos', 'remuneraciones',
            'titulo', 'afps', 'type_aportaciones', 'money'
        );

        return $this;
    }

    private function configRemuneraciones($remuneraciones)
    {
        $this->type_remuneraciones = TypeRemuneracion::where('report', 1)
            ->whereIn("id", $remuneraciones->pluck(['type_remuneracion_id']))
            ->get();
        // config type_remuneraciones
        foreach ($this->type_remuneraciones as $type_remuneracion) {
            // obtenemos los tipos de categorias
            $type_remuneracion->type_categorias = $this->type_categorias;
            // configurar remuneraciones
            foreach ($type_remuneracion->type_categorias as $type_categoria) {
                foreach ($type_categoria->cargos as $cargo) {
                    $cargo->monto = $remuneraciones
                        ->where('type_remuneracion_id')
                        ->where('cargo_id', $cargo->id)
                        ->sum('monto');
                }
            }
            // guardar el total por categoria de las remuneraciones
            $type_remuneracion->total = $remuneraciones
                ->where("type_remuneracion_id", $type_remuneracion->id)
                ->sum("monto");
        }
    }


    private function configDescuentos($descuentos)
    {
        $this->type_descuentos = TypeDescuento::doesntHave('afp_primas')
            ->doesntHave('afp_aportes')
            ->doesntHave('type_afp')
            ->whereIn("id", $descuentos->pluck(['type_descuento_id']))
            ->where('base', 0)
            ->get();
        // configurar descuentos
        foreach ($this->type_descuentos as $type_descuento) {
            $type_descuento->monto = $descuentos
                ->where('type_descuento_id', $type_descuento->id)
                ->sum('monto');
        }
    }


    private function configAportaciones($aportaciones)
    {
        // total
        $total = 0;
        $this->type_aportaciones = TypeAportacion::where('activo', 1)->get();
        // configurar aportaciones
        foreach ($this->type_aportaciones as $aportacion) {
            // guardar monto
            $monto = $aportaciones
                ->where('type_aportacion_id', $aportacion->id)
                ->sum('monto');
            $aportacion->monto += round($monto, 2);
            $total += $aportacion->monto;
        }
        // total de aportaciones
        $this->cronograma->total_aportaciones = $total;
    }

    /**
     * Configurar afps antes de renderizar
     *
     * @return void
     */
    private function configAfps()
    {
        $inTypeAFP = collect();
        // obtenemos los id de los descuentos
        $type_descuentos = TypeDescuento::whereHas('afp_primas')
            ->whereHas('afp_aportes')
            ->orWhereHas('type_afp')
            ->get();
        // configurar afps
        foreach($this->afps as $afp) {
            $historialAFP = $this->historial->where('afp_id', $afp->id)->pluck('id');
            // almacenamos el monto de afp
            $afp_monto = $this->descuentos
                ->whereIn("type_descuento_id", $type_descuentos->pluck(['id']))
                ->whereIn('historial_id', $historialAFP)
                ->sum("monto");
            // almacenamos el monto del afp
            $afp->monto = round($afp_monto, 2);
        }
    }

    /**
     * Renderizar vista en  HTML
     *
     * @return void
     */
    public function render()
    {
        return view($this->view, $this->storage);
    }


    public function stream($name = '')
    {
        $pdf = PDF::loadView($this->view, $this->storage);
        $pdf->setPaper('a3', 'landscape')->setWarnings(false);
        return $pdf->stream($name);
    }

}