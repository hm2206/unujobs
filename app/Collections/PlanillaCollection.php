<?php

namespace App\Collections;

use App\Models\TypeRemuneracion;
use App\Models\TypeCategoria;
use App\Models\TypeDescuento;
use App\Models\Cargo;
use App\Models\Cronograma;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Models\Afp;
use App\Models\Historial;
use App\Notifications\ReportNotification;
use \PDF;
use App\Models\User;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;
use \Carbon\Carbon;
use App\Models\Meta;
use App\Tools\Money;
use App\Models\TypeAportacion;
use App\Models\TypeAfp;


class PlanillaCollection
{

    private $meta;
    private $storage;
    private $cronograma;
    private $view = 'reports.planilla';

    public function __construct(Meta $meta, Cronograma $cronograma)
    {
        $this->meta = $meta;
        $this->cronograma = $cronograma;
    }


    public function generate()
    {
        $meses = [
            "Enero", "Febrero", "Marzo", "Abril", 
            "Mayo", 'Junio', 'Julio', 'Agosto', 
            'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        // almacenar attr en variables locales
        $cronograma = $this->cronograma;
        $meta = $this->meta;
        $meta->mes = $cronograma->mes;
        $meta->year = $cronograma->año;
        $money = new Money;
        $pagina = 1;
        // obtener historial
        $historial = Historial::where("cronograma_id", $cronograma->id)
            ->where('meta_id', $meta->id)
            ->orderBy('orden', 'ASC')
            ->get();
        // cargos
        $cargos = Cargo::whereIn('id', $historial->pluck(['cargo_id']))->get();
        // obtener remuneraciones
        $remuneraciones = Remuneracion::with("typeRemuneracion")->where('show', 1)
            ->whereIn("historial_id", $historial->pluck(['id']))
            ->get();
        // obtener descuentos
        $descuentos = Descuento::with("typeDescuento")->where("base", 0)
            ->whereIn("historial_id", $historial->pluck(['id']))
            ->get();
        // aportaciones
        $aportaciones = Descuento::with("typeDescuento")->where("base", 1)
            ->whereIn("historial_id", $historial->pluck(['id']))
            ->get();
            //traemos trabajadores que pertenescan a la meta actual
        foreach ($historial as $history) {
            // obtenemos las remuneraciones actuales del trabajador
            $tmp_remuneraciones = $remuneraciones->where("historial_id", $history->id);
            // total de remuneraciones
            $total = $history->total_bruto;
            // base imponible
            $tmp_base = $history->base;
            // verificar que no tenga más de 23 
            if ($tmp_remuneraciones->count() < 23) {
                $limite = 23 - $tmp_remuneraciones->count();
                for($i = 0; $limite > $i; $i++) {
                    $tmp_remuneraciones->put(rand(1000, 9999), (Object)[
                        "nivel" => 0,
                        "empty" => true,
                        "key" => "",
                        "monto" => ""
                    ]);
                }
            }
            // agregamos el total bruto
            $tmp_remuneraciones->put(rand(1000, 9999), (Object)[
                "nivel" => 1,
                "key" => "TOTAL",
                "monto" => $total
            ]);
                    
            $history->remuneraciones = $tmp_remuneraciones->chunk(6);

            //obtenemos los descuentos actuales del trabajador
            $tmp_descuentos = $descuentos->where("historial_id", $history->id);
            $total_descto = $history->total_desct;

            //calcular base imponible
            $history->base = $tmp_base;
            //calcular total neto
            $history->neto = $history->total_neto;

            // agregamos el total neto
            $tmp_descuentos->put(rand(1000, 9999), (Object)[
                "nivel" => 1,
                "key" => "DESC",
                "monto" => $total_descto
            ]);

            $history->descuentos = $tmp_descuentos->chunk(6);

            $tmp_aportaciones = $aportaciones->where("historial_id", $history->id);
            $total_aportaciones = $tmp_aportaciones->sum("monto");

            // total de las Aportaciones
            $tmp_aportaciones->put(rand(1000, 9999), (Object)[
                "nivel" => 1,
                "key" => "APORT",
                "monto" => $total_aportaciones
            ]);

            $history->aportaciones = $tmp_aportaciones;

        }

        // agrupar por cargos
        foreach ($cargos as $cargo) {
            $cargo->historial = $historial->where("cargo_id", $cargo->id)->chunk(5);   
        }

        $meta->cargos = $cargos;

        // alamcenar en el storage
        $this->storage = compact('meses', 'meta', 'cronograma', 'pagina', 'money');
    }


    public function getStorage()
    {
        return $this->storage;
    }


    public function render()
    {
        return view($this->view, $this->storage);
    }


    public function stream($name = '')
    {
        $pdf = PDF::loadView('pdf.planilla', $this->storage);
        $pdf->setPaper('a3', 'landscape')->setWarnings(false);
        return $pdf->stream($name);
    }

    
}