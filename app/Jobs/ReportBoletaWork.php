<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Notifications\ReportNotification;
use \PDF;
use App\Models\Historial;
use App\Models\Cronograma;
use App\Models\TypeRemuneracion;
use App\Models\TypeDescuento;
use App\Models\Remuneracion;
use App\Models\Descuento;
use App\Models\User;
use App\Models\Report;
use \Carbon\Carbon;
use App\Models\Work;
use App\Collections\BoletaCollection;
use \DB;

class ReportBoletaWork implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $inHistorial = [];
    private $work;
    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($inHistorial = [], Work $work)
    {
        $this->inHistorial = $inHistorial;
        $this->work = $work;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $historial = Historial::whereIn("id", $this->inHistorial)->get();
        $work = $this->work;
        // obtener remuneraciones
        $remuneraciones = Remuneracion::whereIn("historial_id", $historial->pluck(['id']))
            ->where('show', 1)
            ->get();
        // obtener descuentos y aportaciones
        $descuentos = Descuento::whereIn("historial_id", $historial->pluck(['id']))->get();
        // generar configuracion para las boleta
        $boleta = BoletaCollection::init();
        $boleta->setRemuneraciones($remuneraciones);
        $boleta->setDescuentos($descuentos->where('base', 0));
        $boleta->setAportaciones($descuentos->where('base', 1));
        $boleta->get($historial);
        $pdf = $boleta->generate();
        $pdf->setPaper('a4', 'landscape')->setWarnings(false);
        $nombre = "boletas_" . $work->nombre_completo . date('Y-m-d') . ".pdf";

        $pdf->save(storage_path("app/public/pdf/{$nombre}"));

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification("/storage/pdf/{$nombre}", 
                "La boletas de {$work->nombre_completo} fué generada"
            ));
        }

    }
}
