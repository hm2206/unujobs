<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Work;
use \PDF;
use App\Models\User;
use App\Notifications\ReportNotification;
use App\Models\Report;
use \Carbon\Carbon;
use App\Models\Banco;
use App\Models\TypeRemuneracion;
use App\Models\Descuento;
use App\Models\Remuneracion;

class ReportCheque implements ShouldQueue
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
            "Enero", "Febrero", "Marzo", "Abril", 
            "Mayo", 'Junio', 'Julio', 'Agosto', 
            'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];

        $cronograma = $this->cronograma;
        $infoIn = $cronograma->infos->pluck(['work_id']);
        $tmp_works = Work::whereIn("id", $infoIn)
            ->where("cheque", 1)
            ->orderBy("nombre_completo", "ASC")
            ->get();
            

        // configuracion
        $descuentos = Descuento::whereIn("work_id", $infoIn)
            ->where("cronograma_id", $cronograma->id)
            ->where("base", 0)
            ->get();

        $remuneraciones = Remuneracion::whereIn("work_id", $infoIn)
            ->where("cronograma_id", $cronograma->id)
            ->get();

        $bancos = Banco::whereIn("id", $tmp_works->pluck(["banco_id"]))->get(); 
        $bonificaciones = TypeRemuneracion::where("bonificacion", 1)->get();
        
        foreach ($bancos as $banco) {

            $banco->count = $tmp_works->where("banco_id", $banco->id)->count();
            $banco->works = $tmp_works->where("banco_id", $banco->id);

            foreach ($banco->works as $work) {
                
                $work->bonificaciones = $remuneraciones->where("work_id", $work->id)
                    ->whereIn("type_remuneracion_id", $bonificaciones->pluck(['id']));
                $work->total_neto =  $remuneraciones->where("work_id", $work->id)->sum("monto") - $descuentos->where("work_id", $work->id)->sum('monto');
                
            }

        }
        
        $pdf = PDF::loadView("pdf.reporte_cheque", compact('cronograma', 'bancos', 'meses'));

        $fecha = strtotime(Carbon::now());
        $name = "reporte_cheque_{$fecha}.pdf";
        $pdf->save(storage_path("app/public") . "/pdf/{$name}");

        $archivo = Report::create([
            "type" => "pdf",
            "name" => "Reporte de Cheques del {$cronograma->mes} del {$cronograma->año}",
            "icono" => "fas fa-file-pdf",
            "path" => "/storage/pdf/{$name}",
            "cronograma_id" => $this->cronograma->id,
            "type_report_id" => $this->type_report
        ]);

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification("/storage/pdf/{$name}", "EL Resumen de cheques, {$cronograma->mes} del {$cronograma->year} fué generada"));
        }

    }
}
