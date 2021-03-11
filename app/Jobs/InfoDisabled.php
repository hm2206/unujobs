<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\Info;

class InfoDisabled implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $infos;

    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($infos)
    {
        $this->infos = $infos;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $planillaIn = $this->infos->pluck('planilla_id');
        $cargoIn = $this->infos->pluck('cargo_id');
        $categoriaIn = $this->infos->pluck('categoria_id');
        $workIn = $this->infos->pluck('work_id');
        // desactivar infos
        Info::whereIn('planilla_id', $planillaIn)
            ->whereIn('cargo_id', $cargoIn)
            ->whereIn('categoria_id', $categoriaIn)
            ->whereIn('work_id', $workIn)
            ->updateIn([ 'active' => 0 ]);
    }
}
