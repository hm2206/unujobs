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
use App\Models\Report;

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

    private $config = [];

    /**
     * @param string $url
     * @param string $titulo
     * @return void
     */
    public function __construct($url, $titulo, $config = null)
    {
        $this->url = $url;
        $this->titulo = $titulo;
        $this->config = $config;
    }

    /**
     *  Notifica a los usuarios cuando la exportación de datos
     *  ha sidó terminada
     *
     * @return void
     */
    public function handle()
    {

        if ($this->config) {

            try {
                
                $archivo = Report::create([
                    "type" => $this->config["type"],
                    "name" => $this->config["name"],
                    "icono" => $this->config["icono"],
                    "path" => $this->config["path"],
                    "cronograma_id" => $this->config["cronograma_id"],
                    "type_report_id" => $this->config["type_report"]
                ]);

                \Log::info("listo");

            } catch (\Throwable $th) {

                 \Log::info($th);

            }

        }

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
