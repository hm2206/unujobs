<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use \DB;
use App\Collections\WorkCollection;
use App\Models\User;
use App\Notifications\BasicNotification;

/**
 * Procesa las remuneraciones de los trabajadores
 */
class ProssingRemuneracion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    
    private $cronograma;
    private $jobs;
     

    public $timeout = 0;


    /**
     * @param \App\Models\Cronograma $cronograma
     * @param \App\Models\Work $jobs
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
            $this->configurarRemuneracion($cronograma, $job);
        }

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new BasicNotification("#", "Remuneraciones agregadas a los trabajadores"));
        }
    }

    /**
     * Se encarga de configurar y procesar las remuneraciones de cada trabajador
     *
     * @param \App\Models\TypeRemuneracion $types
     * @param \App\Models\Cronograma $cronograma
     * @param \App\Models\Work $job
     * @return void
     */
    private function configurarRemuneracion($cronograma, $job)
    {
        $collection = new WorkCollection($job);
        $collection->createOrUpdateRemuneracion($cronograma);
    }

}
