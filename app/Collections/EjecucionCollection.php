<?php

namespace App\Collections;


use App\Models\TypeCategoria;
use App\Models\Descuento;
use App\Models\Remuneracion;
use App\Models\TypeRemuneracion;
use App\Models\Meta;
use App\Tools\Money;
use App\Models\Report;
use App\Models\User;
use App\Models\Historial;
use App\Tools\Helpers;
use App\Models\Cargo;
use \PDF;

class EjecucionCollection
{

    private $neto = 0;
    private $cronograma;
    private $storage;
    private $all_remuneraciones;
    private $type_bonificaciones;
    private $bonificaciones;
    private $remuneraciones;
    private $type_categorias;
    private $metas;
    private $descuentos = [];
    private $contenidos = [];
    private $footer = [];
    private $historial = [];
    private $cargos = [];
    private $is_detalle = false;
    private $view = 'reports.ejecucion';


    public function __construct($cronograma, $neto)
    {
        $this->cronograma = $cronograma;
        $this->neto = $neto;
    }


    /**
    * generar la ejecucion
    *
    * @return void
    */
    public function generate()
    {
        // configurar
        $this->config();
        $this->is_detalle ? $this->configDetallados() : $this->configContenidos();
        $this->configBonificaciones();
        $this->configTotales();
        
        // variables locales
        $cronograma = $this->cronograma;
        $mes = Helpers::mes($cronograma->mes);
        $contenidos = $this->contenidos;
        $footer = $this->footer;
        $type_categorias = $this->type_categorias;
        $neto = $this->neto;
        $cargos = $this->cargos;
        $this->storage = compact('cronograma','mes', 'type_categorias', 'contenidos', 'neto', 'footer', 'cargos');
        
    }


    public function setIsDetalle(bool $value)
    {
        $this->is_detalle = $value;
    }

    /**
     * Configuracion de los parametros para la ejecución
     *
     * @return void
     */
    private function config()
    {
        // configuraciones globales
        $cronograma = $this->cronograma;
        $this->all_remuneraciones = Remuneracion::where("show", 1)->where("cronograma_id", $cronograma->id)->get();
        $this->type_bonificaciones = TypeRemuneracion::where("bonificacion", 1)->get();
        $this->metas = Meta::whereIn("id", $this->all_remuneraciones->pluck('meta_id'))->get();
        $this->type_categorias = TypeCategoria::whereHas('cargos', function($c) use($cronograma) {
                $c->where("planilla_id", $cronograma->planilla_id);
            })->with('cargos')->get();

        // otras configuraciones
        if ($this->neto) {
            $this->descuentos = Descuento::where("cronograma_id", $cronograma->id)->get();
        }

        // obtener remuneraciones y descuentos
        $this->bonificaciones = $this->all_remuneraciones->whereIn("type_remuneracion_id", $this->type_bonificaciones->pluck(['id']));
        $this->remuneraciones = $this->all_remuneraciones->whereNotIn("type_remuneracion_id", $this->type_bonificaciones->pluck(['id']));
        // setting contenidos
        $this->contenidos = [
            "metas" => ["size" => "20%", "content" => $this->metas->pluck(['meta'])],
            "actividad" => ["size" => '7%', "content" => $this->metas->pluck(['actividadID'])],
            "meta" => ["size" => '4%', 'content' => $this->metas->pluck(['metaID'])],
        ];
    }


    /**
     * configurar historial de pagos por Cheque y Cuenta
     *
     * @return void
     */
    private function configPagos()
    {
        $cargos = Cargo::where("planilla_id", $this->cronograma->planilla_id)->get();
        // tipos de pago por cuenta
        $cuentas = Historial::where("cronograma_id", $this->cronograma->id)
            ->where("numero_de_cuenta", "<>", "")
            ->get();
        // tipos de pago por cheques
        $cheques = Historial::where('cronograma_id', $this->cronograma->id)
            ->where("numero_de_cuenta", '')
            ->get();

        // almacenar historial
        $this->historial = [
            'cuentas' => $cuentas,
            'cheques' => $cheques
        ];
    }

    /**
     * configurar cargos
     *
     * @return void
     */
    private function configCargos()
    {
        $this->cargos = Cargo::where("planilla_id", $this->cronograma->planilla_id)->get();
        // recorrer los montos por cargo
        foreach ($this->cargos as $cargo) {
                
            foreach ($this->historial as $name => $config) {

                $payload = [];
                $total = 0;
                $size = (100 * 1 ) / $this->cargos->count();
    
                foreach ($this->metas as $meta) {

                    $monto = $this->remuneraciones->where("cargo_id", $cargo->id)
                        ->whereIn("historial_id", $config->pluck(['id']))
                        ->where("meta_id", $meta->id)
                        ->sum("monto");
    
                    if ($this->neto) {
                        $monto = $monto - $this->descuentos->where("base", 0)
                            ->whereIn("historial_id", $config->pluck(['id']))
                            ->where("cargo_id", $cargo->id)
                            ->where("meta_id", $meta->id)
                            ->sum("monto");
                    }
                    
                    $total += $monto;
                    array_push($payload,  Money::parse($monto));
                }
    
                $key = str_replace(" ", "-", strtolower($cargo->descripcion)) . "-{$name}";
                $this->contenidos[$key] = ["size" => $size, "content" => $payload];
                array_push($this->footer, Money::parse($total));

            }
                
        }

        $this->view = 'reports.ejecucion_detalle';
    }


    /**
     * condfiguracion de la ejecución detallada
     *
     * @return void
     */
    public function configDetallados()
    {
        $this->configPagos();
        $this->configCargos();
    }

    /**
     * configura el contenido de la ejecución
     *
     * @return void
     */
    public function configContenidos()
    {
        // recorrer los montos por cargo
        foreach ($this->type_categorias as $categoria) {
            foreach ($categoria->cargos as $cargo) {
                
                $payload = [];
                $total = 0;
                $size = (100 * 1 ) / $categoria->cargos->count();

                foreach ($this->metas as $meta) {
                    $monto = $this->remuneraciones->where("cargo_id", $cargo->id)
                        ->where("meta_id", $meta->id)
                        ->sum("monto");

                    if ($this->neto) {
                        $monto = $monto - $this->descuentos->where("base", 0)
                            ->where("cargo_id", $cargo->id)
                            ->where("meta_id", $meta->id)
                            ->sum("monto");
                    }

                    $total += $monto;
                    array_push($payload,  Money::parse($monto));
                }

                $key = str_replace(" ", "-", strtolower($cargo->descripcion));
                $this->contenidos[$key] = ["size" => $size, "content" => $payload];
                array_push($this->footer, Money::parse($total));
                
            }
        }
    }


    /**
     * configura las bonificaciones de la ejecucion
     *
     * @return void
     */
    private function configBonificaciones()
    {
        if ($this->is_detalle) {
            // recorrer las bonificaciones
            foreach ($this->type_bonificaciones as $bonificacion) {
                
                foreach ($this->historial as $name => $config) {

                    $payload = [];
                    $total = 0;
                    $size = (100 * 1 ) / $this->type_bonificaciones->count();
                    
                    foreach ($this->metas as $meta) {

                        $monto = $this->bonificaciones->where("meta_id", $meta->id)
                            ->whereIn("historial_id", $config->pluck(['id']))
                            ->sum("monto");
                            
                        $total += $monto;
                        array_push($payload, Money::parse($monto));
                    }

                    $this->contenidos[$bonificacion->key . $name] =  ["size" => "{$size}%","content" => $payload];
                    array_push($this->footer, Money::parse($total));
                    
                }

            }
        }else {
            // recorrer las bonificaciones
            foreach ($this->type_bonificaciones as $bonificacion) {
                $payload = [];
                $total = 0;
                $size = (100 * 1 ) / $this->type_bonificaciones->count();

                foreach ($this->metas as $meta) {
                    $monto = $this->bonificaciones->where("meta_id", $meta->id)->sum("monto");
                    $total += $monto;
                    array_push($payload, Money::parse($monto));
                }

                $this->contenidos[$bonificacion->key] =  ["size" => "{$size}%","content" => $payload];
                array_push($this->footer, Money::parse($total));
            }
        }

    }


    /**
     * configura los totales de la ejecucion
     *
     * @return void
     */
    private function configTotales()
    {
        // totales
        $payload = [];
        $total = 0;
        foreach ($this->metas as $meta) {
            $monto = $this->all_remuneraciones->where("meta_id", $meta->id)->sum("monto");

            if ($this->neto) {
                $monto = $monto - $this->descuentos->where("base", 0)
                    ->where("meta_id", $meta->id)
                    ->sum("monto");
            }

            $total += $monto;
            array_push($payload, Money::parse($monto));
        }

        $this->contenidos["totales"] = ["size" => "10%", "content" => $payload];
        array_push($this->footer, Money::parse($total));
    }


    public function getStorage()
    {
        return $this->storage;
    }


    public function render()
    {
        return view($this->view, $this->storage);
    }
}
