<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Remuneracion;
use App\Models\TypeRemuneracion;
use App\Models\TypeDescuento;
use App\Models\Descuento;
use App\Models\Historial;
use App\Models\Report;
use App\Notifications\ReportNotification;
use App\Models\User;
use App\Tools\Money;
use \PDF;

class ReportAFP implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $timeout = 0;

    private $cronograma;
    private $afp;
    private $type_report;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($cronograma, $afp, $type_report)
    {
        $this->cronograma = $cronograma;
        $this->afp = $afp;
        $this->type_report = $type_report;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        $cronograma = $this->cronograma;
        $afp = $this->afp;
        $pages = [];
        $money = new Money;

        $meses = [
            "Enero", "Febrero", "Marzo", "Abril", 
            "Mayo", 'Junio', 'Julio', 'Agosto', 
            'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];

        $typeDescuentos = TypeDescuento::whereIn("key", ["32", "34", "35"])->get();
        $historial = Historial::with('afp', 'work')->where("afp_id", $afp->id)
            ->orderBy('orden', 'ASC')
            ->where('cronograma_id', $cronograma->id)->get();
        // obtener los descuentos
        $descuentos = Descuento::whereIn("type_descuento_id", $typeDescuentos->pluck(['id']))
            ->whereIn("historial_id", $historial->pluck(['id']))
            ->get();

        $remuneraciones = Remuneracion::where("show", 1)->where("cronograma_id", $this->cronograma->id)
            ->whereIn("work_id", $historial->pluck(['id']))
            ->get();

        foreach ($historial as $history) {
            
            // monto del aporte afp
            $history->pension = $descuentos->where("historial_id", $history->id)
                ->where('type_descuento_id', $typeDescuentos->where('key', '32')->first()->id)
                ->sum('monto');
            // monto comision variable
            $history->ca = $descuentos->where("historial_id", $history->id)
                ->where('type_descuento_id', $typeDescuentos->where('key', '34')->first()->id)
                ->sum('monto');
            // monto del prima de seguro
            $history->prima_seg = $descuentos->where("historial_id", $history->id)
                ->where('type_descuento_id', $typeDescuentos->where('key', '35')->first()->id)
                ->sum('monto');

        }

        $pages = $historial->chunk(52);

        $path = "pdf/report_afp_{$afp->nombre}_{$cronograma->id}.pdf";
        $nombre = "Reporte AFP {$afp->nombre}";

        $pdf = PDF::loadView("pdf.reporte_afp", compact('pages', 'afp', 'cronograma', 'meses', 'money'));
        $pdf->setPaper("a4", 'portrait')->setWarnings(false);
        $pdf->save(storage_path("app/public/{$path}"));

        $archivo = Report::where("cronograma_id", $cronograma->id)
            ->where("type_report_id", $this->type_report)
            ->where("name", $nombre)
            ->first();

        if ($archivo) {
            $archivo->update([
                "read" => 0,
                "path" => "/storage/{$path}"
            ]);
        }else {
            $archivo = Report::create([
                "type" => "pdf",
                "name" => $nombre,
                "icono" => "fas fa-file-pdf",
                "path" => "/storage/{$path}",
                "cronograma_id" => $cronograma->id,
                "type_report_id" => $this->type_report
            ]);
        }

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification($archivo->path, $archivo->name));
        }

    }
}
