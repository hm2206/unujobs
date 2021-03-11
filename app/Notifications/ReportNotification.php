<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Notificación de reportes de pdf
 */
class ReportNotification extends Notification
{
    use Queueable;

    private $url;
    private $body;


    public function __construct($url, $body)
    {
        $this->url = $url;
        $this->body = $body;
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
            "icono" => "fas fa-file-pdf",
            "background" => "bg-danger"
        ];
    }
}
