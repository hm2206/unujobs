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
use App\Mail\SendBoleta;
use App\Notifications\ReportNotification;

/**
 * Generar Archivo PDF de las Boletas mesuales de un determinado cronograma
 */
class ReportBoleta implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $mes;
    private $year;
    private $adicional;

    /**
     * @param string $mes
     * @param string $year
     * @param string $adicional
     */
    public function __construct($mes, $year, $adicional)
    {
        $this->mes = $mes;
        $this->year = $year;   
        $this->adicional = $adicional;
    }

    /**
     * Genera y procesa las boletas de los trabajadores
     *
     * @return void
     */
    public function handle()
    {

        $workIn = Remuneracion::where("mes", $this->mes)
            ->where("año", $this->year)
            ->where('adicional', $this->adicional)
            ->get()
            ->pluck(["work_id"]);

        $works = Work::whereIn("id", $workIn)->get();

        foreach ($works as $work) {
            
            foreach ($work->infos as $info) {

                $remuneraciones = Remuneracion::where("work_id", $work->id)
                    ->where("categoria_id", $info->categoria_id)
                    ->where("cargo_id", $info->cargo_id)
                    ->where("planilla_id", $info->planilla_id)
                    ->where("adicional", $this->adicional)
                    ->where("mes", $this->mes)
                    ->where("año", $this->year)
                    ->get();

                $descuentos = Descuento::with('typeDescuento')->where('work_id', $work->id)
                    ->where("categoria_id", $info->categoria_id)
                    ->where("cargo_id", $info->cargo_id)
                    ->where("planilla_id", $info->planilla_id)
                    ->where("adicional", $this->adicional)
                    ->where("mes", $this->mes)
                    ->where("año", $this->year)
                    ->get();

                $total = $remuneraciones->sum("monto");
                
                $info->remuneraciones = $remuneraciones;
                $info->descuentos = $descuentos->chunk(2)->toArray();
                $info->total_descuento = $descuentos->sum('monto');


                //base imponible
                $info->base = $remuneraciones->where('base', 0)->sum('monto');

                //aportes
                $info->essalud = $info->base < 930 ? 83.7 : $info->base * 0.09;
                $info->accidentes = $work->accidentes ? ($info->base * 1.55) / 100 : 0;

                //total neto
                $info->neto = $total - $info->total_descuento;
                $info->total_aportes = $info->essalud + $info->accidentes;

            }

            if ($work->email) {

                try {
                    $year = $this->year;
                    $mes = $this->mes;
                    $pdf_tmp = PDF::loadView('pdf.send_boleta', compact('work', 'year', 'mes'));
                    $pdf_tmp->setPaper('a4', 'landscape');

                    Mail::to($work->email)
                    ->send(new SendBoleta($work, $this->year, $this->mes, $this->adicional, $pdf_tmp));
                } catch (\Throwable $th) {
                    \Log::info('No se pudó enviar boleta de: ' . $work->email . " error: " . $th);
                }

            }

        }

        
        //genera el pdf;
        $pdf = PDF::loadView("pdf.boleta_auto", compact('works'));
        $pdf->setPaper('a4', 'landscape')->setWarnings(false);
        $pdf->save(storage_path("app/public/pdf/boletas_{$this->mes}_{$this->year}_{$this->adicional}.pdf"));

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification("/storage/pdf/boletas_{$this->mes}_{$this->year}_{$this->adicional}.pdf", 
                "La boleta {$this->mes} del {$this->year} fué generada"
            ));
        }

    }
}
