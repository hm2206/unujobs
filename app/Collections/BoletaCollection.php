<?php

namespace App\Collections;

use App\Models\Historial;
use App\Models\Cronograma;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Models\TypeRemuneraciones;
use \PDF;
use App\Tools\Money;

class BoletaCollection
{

    private $errors = [];
    private $cronograma;
    private $remuneraciones;
    private $descuentos;
    private $aportaciones;
    private $storage;
    private $store;
    private $page = 0;
    private $rows = 23;
    private $mes = 9;
    private $year = 2019;
    /**
     * lista de estilos estilos
     *
     * @var array
     */
    private $styles = [
        'app' => ['css' => '/css/app.css', 'media' => ''],
        'pdf' => ['css' => '/css/pdf.css', 'media' => ''],
        'default' => ['css' => '/css/print/boleta.css', 'media' => 'print'],
    ];
    /**
     * reglas para los recursos
     *
     * @var array
     */
    private $ruleResource = [
        "http" => 'asset', 
        "file" => 'public_path'
    ];
    private $resource = 'file';
    // arreglo de meses
    private $meses =  [
        "Enero", "Febrero", "Marzo", "Abril", 
        "Mayo", 'Junio', 'Julio', 'Agosto', 
        'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    public function __construct($storage = [], $remuneraciones = [], $descuentos = [], $aportaciones = [])
    {
        $this->storage = $storage;
        $this->remuneraciones = $remuneraciones;
        $this->descuentos = $descuentos;
        $this->aportaciones = $aportaciones;
    }


    public static function init()
    {
        return new self;
    }


    public function getStorage()
    {
        return $this->storage;
    }
    

    public function setRemuneraciones($remuneraciones)
    { 
        $this->remuneraciones = $remuneraciones;
    }


    public function setDescuentos($descuentos)
    {
        $this->descuentos = $descuentos;
    }


    public function setAportaciones($aportaciones)
    {
        $this->aportaciones = $aportaciones;
    }


    public function setMes($newMes)
    {
        $this->mes = $newMes;
    }


    public function setStyle($key, array $values = []) {
        $this->styles[$key][$values[0]] = $values[1];
    }


    public function setYear($newYear)
    {
        $this->year = $newYear;
    }

    public function setRows($newRow)
    {
        $this->rows = $newRow;
    }


    public function setResource($resource)
    {
        $this->ruleResource[$resource];
        $this->resource = $resource;
    }


    public function find(Historial $history)
    {   
        $store = collect();
        $this->page += 1;
        try {
            // obtener datos del trabajador
            $work = $history->work;
            $cargo = $history->cargo;
            $categoria = $history->categoria;
            $meta = $history->meta;
            $afp = $history->afp;
            // obtener remuneraciones
            $remuneraciones = $this->remuneraciones->where('historial_id', $history->id);
            // obtener descuentos
            $descuentos = $this->descuentos->where('historial_id', $history->id);
            // obtener aportaciones
            $aportaciones = $this->aportaciones->where('historial_id', $history->id);
            // guardar en el store los datos obtenidos
            $store->put("head", $work->nombre_completo);
            $store->put("remuneraciones", $remuneraciones);
            $store->put("descuentos", $descuentos);
            $store->put("aportaciones", $aportaciones);
            $store->put("history", $history);
            $store->put("work", $work);
            $store->put("cargo", $cargo);
            $store->put("categoria", $categoria);
            $store->put("meta", $meta);
            $store->put("afp", $afp);
            $store->put("page", $this->page);
            $store->put("rows", $this->rows);
            $store->put("mes", $this->meses[$this->mes - 1]);
            $store->put("year", $this->year);
            $store->put("rows", $this->rows);
        } catch (\Throwable $th) {
            array_push($this->errors, $th);
        }
        // guardar store
        $this->store = $store;
        // devolvemos la el mismo objeto
        return $store;
    }


    public function pdf()
    {
        $store = $this->store;
        $money = new Money();
        return PDF::loadView('pdf.boleta', compact('store', 'money'));
    }


    public function get($historial)
    {   
        $this->storage = collect();
        foreach ($historial as $history) {
            try {
                $store = $this->find($history);
                $this->storage->push($store);
            } catch (\Throwable $th) {
            }
        }

        return $this->storage;
    }


    public function generate($titulo = '')
    {
        $storage = $this->storage->chunk(2);
        $ruleResource = $this->ruleResource;
        $resource = $this->resource;
        $money = new Money();
        return PDF::loadView('pdf.boleta_auto', compact('storage', 'money', 'titulo', 'ruleResource', 'resource'));
    }


    public function view($titulo = '') 
    {
        $storage = $this->storage->chunk(2);
        $styles = $this->styles;
        $money = new Money();
        return view("reports.boleta", compact('storage', 'money', 'titulo', 'styles'));
    }

}