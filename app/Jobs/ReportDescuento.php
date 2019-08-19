<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\Descuento;
use App\Models\Remuneracion;
use \PDF;
use App\Models\Report;
use \Carbon\Carbon;
use App\Models\User;
use App\Notifications\ReportNotification;
use App\Models\TypeDescuento;
use App\Models\TypeRemuneracion;

class ReportDescuento implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $cronograma;
    private $type_report;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($cronograma, $type_report)
    {
        $this->cronograma = $cronograma;
        $this->type_report = $type_report;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        $meses = $meses = [
            "Enero", "Febrero", "Marzo", "Abril", "Mayo", 
            'Junio', 'Julio', 'Agosto', 'Septiembre', 
            'Octubre', 'Noviembre', 'Diciembre'
        ];

        $cronograma = $this->cronograma;
        $works = $cronograma->works;

        foreach ($works as $work) {
            
            $work->tmp_infos = $work->infos->where("planilla_id", $cronograma->planilla_id);

            foreach ($work->tmp_infos as $count => $info) {
                
                $descuentos = Descuento::where("work_id", $work->id)
                    ->where("cronograma_id", $cronograma->id)
                    ->where("cargo_id", $info->cargo_id)
                    ->where("categoria_id", $info->categoria_id)
                    ->get();

                $info->descuentos = $descuentos->where("base", 0);
                $info->aportaciones = $descuentos->where("base", 1);
                $info->total_descuentos = $descuentos->where("base", 0)->sum('monto');

                $remuneraciones = Remuneracion::where("work_id", $work->id)
                    ->where("cronograma_id", $cronograma->id)
                    ->where("cargo_id", $info->cargo_id)
                    ->where("categoria_id", $info->categoria_id)
                    ->get();

                $info->total_bruto = $remuneraciones->sum('monto');
                $info->base_imponible = $remuneraciones->sum("base", 1);
                $info->total_neto = $info->total_bruto - $info->total_descuentos;
                $info->count = $count + 1;

            }

        }

        // crear pdf
        $pdf = PDF::loadView("pdf.descuento", compact('cronograma', 'works', 'meses'));
        $pdf->setPaper('a4', 'landscape')->setWarnings(false);

        $fecha = strtotime(Carbon::now());
        $name = "descuento_{$fecha}.pdf";
        $pdf->save(storage_path("app/public") . "/pdf/{$name}");

        $archivo = Report::create([
            "type" => "pdf",
            "name" => "Planilla del {$cronograma->mes} del {$cronograma->año}",
            "icono" => "fas fa-file-pdf",
            "path" => "/storage/pdf/{$name}",
            "cronograma_id" => $cronograma->id,
            "type_report_id" => $this->type_report
        ]);


        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification("/storage/pdf/{$name}", "El descuento del {$cronograma->mes} del {$cronograma->year} fué generada"));
        }

    }
}
