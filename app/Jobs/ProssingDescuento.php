<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use \DB;
use App\Models\Work;
use App\Models\Cronograma;
use App\Models\User;
use App\Notifications\BasicNotification;
use App\Collections\WorkCollection;

/**
 * Procesa los descuentos de los trabajadores
 */
class ProssingDescuento implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $cronograma;
    private $jobs;

    public $timeout = 0;

    /**
     * @param object $cronograma
     * @param object $jobs
     * @return void
     */
    public function __construct($cronograma, $jobs)
    {
        $this->cronograma = $cronograma;
        $this->jobs = $jobs;
    }


    /**
     * Se ejecuta automaticamente en las colas de trabajos
     *
     * @return void
     */
    public function handle()
    {
        $cronograma = $this->cronograma;
        $jobs = $this->jobs;

        foreach ($jobs as $job) {
            $this->configurarDescuento($cronograma, $job);
        }

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new BasicNotification("#", "Descuentos agregados a los trabajadores", "fas fa-file-alt", "bg-danger"));
        }

    }


    /**
     * Se encarga de configurar y procesar los descuentos de cada trabajador
     *
     * @param \App\Models\TypeDescuento $types
     * @param \App\Models\Cronograma $cronograma
     * @param \App\Models\Work $job
     * @return void
     */
    private function configurarDescuento($cronograma, $job)
   {
        $collecion = new WorkCollection($job);
        $collecion->updateOrCreateDescuento($cronograma);
   }


}
