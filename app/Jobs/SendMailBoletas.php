<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\Cronograma;
use App\Models\Historial;
use Illuminate\Support\Facades\Storage;
use \Mail;
use App\Mail\SendBoleta;


class SendMailBoletas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $cronograma;
    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Cronograma $cronograma)
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
        // obtener historial con los email
        $historial = Historial::whereHas('work', function($w) {
                $w->where('email', '<>', null);
            })->where('cronograma_id', $this->cronograma->id)
            ->get();
        // recorrer historial
        foreach ($historial as $history) {
            // verificar boleta
            if ($history->boleta && $history->work->email) {
                // obtener trabajador
                try {
                    Mail::to($history->work->email)
                        ->send(new SendBoleta($history, $history->work, $this->cronograma, $history->boleta));
                } catch (\Throwable $th) {
                    \Log::info($th);
                }
            }
        }
    }
}
