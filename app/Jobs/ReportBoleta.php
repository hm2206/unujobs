<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\Work;
use App\Models\Cronograma;
use App\Models\TypeRemuneracion;
use App\Models\TypeDescuento;
use App\Models\Remuneracion;
use App\Models\Descuento;
use \PDF;
use \Mail;
use App\Models\User;
use \Carbon\Carbon;
use App\Mail\SendBoleta;
use App\Models\Report;
use App\Notifications\ReportNotification;

/**
 * Generar Archivo PDF de las Boletas mesuales de un determinado cronograma
 */
class ReportBoleta implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  
    public $timeout = 0;
    private $cronograma;
    private $type_report;

    /**
     * @param string $mes
     * @param string $year
     * @param string $adicional
     */
    public function __construct($cronograma, $type_report)
    {
        $this->cronograma = $cronograma;
        $this->type_report = $type_report;
    }

    /**
     * Genera y procesa las boletas de los trabajadores
     *
     * @return void
     */
    public function handle()
    {

        $works = $this->cronograma->works;
        $cronograma = $this->cronograma;

        foreach ($works as $work) {
            
            $infos = $work->infos->where("planilla_id", $cronograma->planilla_id);
            $work->infos = $infos;

            foreach ($work->infos as $info) {

                $remuneraciones = Remuneracion::orderBy('type_remuneracion_id', 'ASC')
                    ->where("work_id", $work->id)
                    ->where("categoria_id", $info->categoria_id)
                    ->where("cargo_id", $info->cargo_id)
                    ->where("planilla_id", $info->planilla_id)
                    ->where("cronograma_id", $this->cronograma->id)
                    ->get();

                $descuentos = Descuento::with('typeDescuento')
                    ->where('work_id', $work->id)
                    ->where("categoria_id", $info->categoria_id)
                    ->where("cargo_id", $info->cargo_id)
                    ->where("planilla_id", $info->planilla_id)
                    ->where("cronograma_id", $this->cronograma->id)
                    ->get();

                $total = $remuneraciones->sum("monto");
                
                $info->remuneraciones = $remuneraciones;
                $info->descuentos = $descuentos->where("base", 0)->chunk(2)->toArray();
                $info->total_descuento = $descuentos->sum('monto');


                //base imponible
                $info->base = $remuneraciones->where('base', 0)->sum('monto');

                //aportes
                //$info->accidentes = $work->accidentes ? ($info->base * 1.55) / 100 : 0;
                $info->aportaciones = $descuentos->where("base", 1); 

                //total neto
                $info->neto = $total - $info->total_descuento;
                $info->total_aportes = $info->aportaciones->sum('monto');

            }

            if ($work->email) {

                try {
                    $pdf_tmp = PDF::loadView('pdf.send_boleta', compact('work', 'cronograma'));
                    $pdf_tmp->setPaper('a4', 'landscape');

                    Mail::to($work->email)
                        ->send(new SendBoleta($work, $cronograma, $pdf_tmp));

                } catch (\Throwable $th) {
                    \Log::info('No se pudó enviar boleta de: ' . $work->email . " error: " . $th);
                }

            }

        }


        $fecha = strtotime(Carbon::now());
        $name = "boletas_{$this->cronograma->mes}_{$this->cronograma->año}_{$this->cronograma->adicional}_{$fecha}.pdf";
        
        //genera el pdf;
        $pdf = PDF::loadView("pdf.boleta_auto", compact('works', 'cronograma'));
        $pdf->setPaper('a4', 'landscape')->setWarnings(false);
        $pdf->save(storage_path("app/public/pdf/{$name}"));

        $archivo = Report::create([
            "type" => "pdf",
            "name" => "Boletas del {$cronograma->mes} del {$cronograma->año}",
            "icono" => "fas fa-file-pdf",
            "path" => "/storage/pdf/{$name}",
            "cronograma_id" => $this->cronograma->id,
            "type_report_id" => $this->type_report
        ]);

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification("/storage/pdf/{$name}", 
                "La boleta {$this->cronograma->mes} del {$this->cronograma->año} fué generada"
            ));
        }

    }
}
