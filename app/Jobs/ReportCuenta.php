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

class ReportCuenta implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $cronograma;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($cronograma)
    {
        $this->cronograma = $cronograma;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $cronograma = $this->cronograma;
        
        $works = $cronograma->works->where("banco_id", "<>", null);
        
        $pdf = PDF::loadView("pdf.reporte_cuenta", compact('cronograma', 'works'));
        $pdf->setPaper('a3', 'landscape')->setWarnings(false);

        $ruta = "/pdf/reporte_cuenta_{$cronograma->mes}_{$cronograma->year}_{$cronograma->adicional}.pdf";
        $pdf->save(storage_path("app/public") . $ruta);

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new ReportNotification("/storage{$ruta}", "EL Resumen de cuentas, {$cronograma->mes} del {$cronograma->year} fué generada"));
        }

    }
}
