<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use \DB;
use App\Models\Cronograma;
use App\Models\TypeRemuneracion;
use App\Models\TypeDescuento;
use App\Models\Remuneracion;
use App\Models\Work;

class CronogramaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $cronograma;

 
    public function __construct(Cronograma $cronograma)
    {
        $this->cronograma = $cronograma;
    }


    public function handle()
    {

    }





    public function generarDescuento($cronograma)
    {
        
    }


    

}
