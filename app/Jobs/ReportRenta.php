<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\TypeRemuneracion;
use App\Models\TypeDescuento;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Models\User;
use App\Notifications\ReportNotification;
use \PDF;
use \Carbon\Carbon;

class ReportRenta implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;

    private $info;
    private $cronogramas;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($info, $cronogramas)
    {
        $this->info = $info;
        $this->cronogramas = $cronogramas;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        // meses
        $meses = $meses = [
            "ENERO", "FEBRERO", "MARZO", "ABRIL", 
            "MAYO", 'JUNIO', 'JULIO', 'AGOSTO', 
            'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'
        ];
        
        // agregar atributos a variables locales
        $cronogramas = $this->cronogramas;
        $info = $this->info;
        
        // configuración
        $work = $info->work;
        $typeRemuneraciones = TypeRemuneracion::get(['id', 'key', 'descripcion']);
        $remuneraciones =  Remuneracion::where("info_id", $info->id)->whereIn("cronograma_id", $cronogramas->pluck(['id']))->get();

        // parsear descuentos
        $typeDescuento = TypeDescuento::get(["id", "key", "descripcion", "base"]); 
        $typeAportes = $typeDescuento->where("base", 1);
        $typeDscto1 = TypeDescuento::where("base", 0)->take(25)->get(["id", "key", "descripcion", "base"]);
        $typeDscto2 = TypeDescuento::where("base", 0)->whereNotIn("id", $typeDscto1->pluck(['id']))->take(25)
            ->get(["id", "key", "descripcion", "base"]);
        // obtener descuentos
        $descuentos =  Descuento::where("info_id", $info->id)->whereIn("cronograma_id", $cronogramas->pluck(['id']))->get();

        // storages
        $pages = [];
        $storage = [
            [
                "type" => $typeRemuneraciones, 
                "body" => $remuneraciones, 
                "key" => "type_remuneracion_id", 
                "total" => true,
                "txt_total" => "TOTALES REMUNERACIONES",
                "linea" => false,
                "sub_total" => false,
                "neto" => false,
                "txt_sub_total" => "",
                "child" => false,
                "children" => []
            ],
            [
                "type" => $typeDscto1, 
                "body" => $descuentos, 
                "key" => "type_descuento_id", 
                "total" => false,
                "txt_total" => "TOTALES DESCUENTOS",
                "linea" => false,
                "sub_total" => false,
                "neto" => false,
                "txt_sub_total" => "",
                "child" => false,
                "children" => []
            ],
            [
                "type" => $typeDscto2, 
                "body" => $descuentos, 
                "key" => "type_descuento_id", 
                "total" => false,
                "txt_total" => "TOTALES DESCUENTOS",
                "linea" => false,
                "sub_total" => true,
                "neto" => true,
                "txt_sub_total" => "TOTAL DSCTO",
                "child" => true,
                "children" => [
                    "resource" => $typeAportes,
                    "titulo" => "APORTES EMPLEADOR",
                    "count" => $typeAportes->count(),
                    "body" => $typeAportes->pluck(['descripcion'])
                ]
            ]
        ];

        // procesos
        foreach ($storage as $store) {

            $payload = [];
            $totales = [];
            $neto = 0;
            $sub_total = 0;
            $child = 0;

            // configurar detalles x cronograma
            foreach ($cronogramas as $cro) {

                $mes = $meses[$cro->mes - 1];
                $year = $cro->año;
                $categoria = $info->categoria ? $info->categoria->nombre : '';

                // almacenar el total x cronograma
                $total = 0;

                $montos = [
                    $mes,
                    $year,
                    $categoria,
                ];

                // configurar montos
                foreach ($store['type'] as $type) {
                    $monto = $store['body']->where("cronograma_id", $cro->id)
                        ->where($store['key'], $type->id)
                        ->sum("monto");

                    $total += $monto;
                    array_push($montos, $monto);
                }

                // verificar si soporta sub total
                if ($store['sub_total']) {
                    $tmp_sub_total = $store['body']->sum('monto');
                    $sub_total += $tmp_sub_total;
                    \array_push($montos, $tmp_sub_total);
                }

                // verificar si soporta neto
                if ($store['neto']) {
                    $bruto = $remuneraciones->where("cronograma_id", $cro->id)->sum('monto');
                    $total_dsctos = $descuentos->where("cronograma_id", $cro->id)->where('base', 0)->sum('monto');
                    $tmp_neto = $bruto - $total_dsctos;
                    $neto += $tmp_neto;
                    array_push($montos, $tmp_neto);
                }

                // verificar si soporta child
                if ($store['child']) {
                    $local_child = 0;
                    // configurar child
                    foreach ($store['children']['resource'] as $res) {
                        $tmp_monto = $store['body']->where("cronograma_id", $cro->id)
                            ->where($store['key'], $res->id)
                            ->sum("monto");
    
                        $child += $tmp_monto;
                        $local_child += $tmp_monto;
                        array_push($montos, $tmp_monto);
                    }

                    // total de los child x cronograma
                    array_push($montos, $local_child);
                }

                // verificar si soporta total
                if ($store['total']) {
                    array_push($montos, $total);
                }

                // guardar los totales x type y cronograma
                array_push($payload, $montos);
            }
            
            // configurar totales
            foreach ($store['type'] as $type) {
                // guardar los totales x type de todos los cronogramas
                $tmp_totales = $store['body']->where($store['key'], $type->id)->sum('monto');
                array_push($totales, $tmp_totales);
            }

            // verificar si tiene sub totales
            if ($store['sub_total']) {
                array_push($totales, $sub_total);
            }

            // verificar si tiene netos en los totales
            if ($store['neto']) {
                array_push($totales, $neto);
            }

            // verificar si tiene child
            if ($store['child']) {
                $local_total = 0;
                foreach ($store['children']['resource'] as $res) {
                    // guardar los totales x type de todos los cronogramas
                    $tmp_child = $store['body']->where($store['key'], $res->id)->sum('monto');
                    $local_total += $tmp_child; 
                    array_push($totales, $tmp_child);
                }

                // agregar totales de los child al global
                array_push($totales, $local_total);
            }

            // verificar si tiene totales
            if ($store['total']) {
                $tmp_totales = $store['body']->sum('monto');
                array_push($totales, $tmp_totales);
            }

            // preparar configuración
            array_push($pages, [
                "is_total" => $store['total'],
                "is_sub_total" => $store['sub_total'],
                "is_neto" => $store['neto'],
                "header" => $store['type'],
                "bodies" => $payload,
                "totales" => $totales,
                "txt_sub_total" => $store['txt_sub_total'],
                "txt_total" => $store['txt_total'],
                "linea" => $store['linea'],
                "footer" => $store['type']->chunk(5),
                "is_child" => $store['child'],
                "children" => $store['children']
            ]);

        }

        // crear pdf
        $fecha = strtotime(Carbon::now());
        $name = "pdf/report_renta_{$work->numero_de_documento}_{$fecha}.pdf";
        
        // configurar pdf
        $pdf = PDF::loadView("pdf.report_renta", compact('info', 'work', 'pages'));
        $pdf->setPaper('a3', 'landspace');
        $pdf->save(storage_path("app/public") . "/{$name}");

        // notificar
        $users = User::all();

        // enviar notficación
        foreach ($users as $user) {
            $user->notify(new ReportNotification("/storage/{$name}", "El Reporte de Renta de {$work->nombre_completo} fué generada"));
        }

    }
}
