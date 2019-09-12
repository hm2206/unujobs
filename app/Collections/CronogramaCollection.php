<?php

namespace App\Collections;


use App\Models\Work;
use App\Models\Cronograma;
use App\Models\TypeRemuneracion;
use App\Models\TypeDescuento;
use App\Models\Remuneracion;
use App\Models\Descuento;
use \PDF;
use \Mail;
use \DB;
use App\Models\Info;
use App\Models\User;
use \Carbon\Carbon;
use App\Mail\SendBoleta;
use App\Models\Report;
use App\Notifications\ReportNotification;


class CronogramaCollection 
{

    public $cronograma;
    public $works;
    public $count;

    public function __construct($cronograma)
    {
        $this->cronograma = $cronograma;
    }


    public function boleta($infos)
    {
        $count = 1;
        $cronograma = $this->cronograma;

        // obtenemos todas las remuneraciones del cronograma
        $remuneraciones = Remuneracion::with("typeRemuneracion")
            ->orderBy("type_remuneracion_id", "ASC")
            ->whereIn("info_id", $infos->pluck(["id"]))
            ->where("cronograma_id", $cronograma->id)
            ->get();
            
        // obtenemos todos los descuentos del cronograma
        $descuentos = Descuento::with("typeDescuento")
            ->orderBy("type_descuento_id", "ASC")
            ->whereIn("info_id", $infos->pluck(["id"]))
            ->where("cronograma_id", $cronograma->id)
            ->get(); 

        foreach ($infos as $info) {

            $tmp_remuneraciones = $remuneraciones->where("info_id", $info->id);
            $tmp_descuentos = $descuentos->where('info_id', $info->id);

                
            $info->remuneraciones = $tmp_remuneraciones;
            // total de remuneraciones
            $total_remuneracion = $tmp_remuneraciones->sum("monto");
            //base imponible
            $info->base = $tmp_remuneraciones->where('base', 0)->sum('monto');
            $info->remuneraciones->put(rand(1000, 9999), (Object)[
                "nivel" => 1,
                "nombre" => "TOTAL",
                "monto" =>  $tmp_remuneraciones->sum("monto")
            ]);

            $info->descuentos = $tmp_descuentos->where("base", 0);
            // total de descuentos
            $total_descuento = $info->descuentos->sum("monto");
            $info->descuentos->put(rand(1000, 9999), (Object)[
                "nivel" => 1,
                "nombre" => "TOTAL DESCUENTOS",
                "monto" =>  $total_descuento
            ]);

            $info->descuentos = $info->descuentos->chunk(24);

            //aportes
            //$info->accidentes = $work->accidentes ? ($info->base * 1.55) / 100 : 0;
            $info->aportaciones = $tmp_descuentos->where("base", 1);
            // total de aportaciones
            $total_aportes = $info->aportaciones->sum("monto");
            $info->aportaciones->put(rand(1000, 9999), (Object)[
                "nivel" => 1,
                "nombre" => "TOTAL APORTE",
                "monto" =>  $total_aportes
            ]);

            //total neto
            $info->neto = $total_remuneracion - $total_descuento;

            $info->num = $count++;

            if ($info->work) {

                $work = $info->work;

                try {
                    
                    if ($work->email) {
                        $pdf_tmp = PDF::loadView('pdf.send_boleta', compact('info', 'work', 'cronograma'));
                        $pdf_tmp->setPaper('a4', 'landscape');

                        Mail::to($work->email)
                            ->send(new SendBoleta($info, $work, $cronograma, $pdf_tmp));
                    }

                } catch (\Throwable $th) {

                    \Log::info('No se pudó enviar boleta de: ' . $work->email . " error: " . $th);
                    
                }

            }

        }
        

        return [
            "infos" => $infos,
            "cronograma" => $cronograma,
            "count" => $count
        ];
    }

    
}