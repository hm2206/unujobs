<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Notificación Basica
 */
class BasicNotification extends Notification
{
    use Queueable;

    private $url;
    private $body;
    private $icono;
    private $background;

    /**
     * Configuración basica para la notificación
     *
     * @param string $url
     * @param string $body
     * @param string $icono
     * @param string $background
     */
    public function __construct($url = "", $body = "", $icono = "fas fa-file-alt", $background = "bg-primary")
    {
        $this->url = $url;
        $this->body = $body;
        $this->icono = $icono;
        $this->background = $background;
    }

    
    public function via($notifiable)
    {
        return ['database'];
    }


    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }


    public function toArray($notifiable)
    {
        return [
            "url" => $this->url,
            "body" => $this->body,
            "icono" => $this->icono,
            "background" => $this->background
        ];
    }
}
