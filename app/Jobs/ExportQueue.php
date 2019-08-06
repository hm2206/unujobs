<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\BasicNotification;
use App\Models\User;
use \Excel;

/**
 * Notificar cuando la exportación a concluido
 */
class ExportQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Almacenamos una url que será mostrada al usuario
     *
     * @var string
     */
    private $url;

    /**
     * Almacenamos un titulo que será mostrado al usuario en las notificaciones
     *
     * @var string
     */
    private $titulo;

    /**
     * @param string $url
     * @param string $titulo
     * @return void
     */
    public function __construct($url, $titulo)
    {
        $this->url = $url;
        $this->titulo = $titulo;
    }

    /**
     *  Notifica a los usuarios cuando la exportación de datos
     *  ha sidó terminada
     *
     * @return void
     */
    public function handle()
    {

        $users = User::all();
    
        foreach ($users as $user) {
            $user->notify(new BasicNotification(
                $this->url, 
                "La exportación {$this->titulo}, ya está lista!",
                "fas fa-file-excel",
                "bg-success"
            ));
        }

    }
}
