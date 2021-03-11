<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Work;

/**
 * Modelo de mail para notificar al trabajador sobre su boleta
 */
class SendBoleta extends Mailable
{
    use Queueable, SerializesModels;

    public $cronograma;
    public $history;
    public $work;
    public $pdf;

    /**
     * @param \App\Models\Work $work
     * @param string $year
     * @param string $mes
     * @param string $adicional
     * @param \PDF $pdf
     */
    public function __construct($history, $work, $cronograma, $pdf)
    {
        $this->history = $history;
        $this->work = $work;
        $this->cronograma = $cronograma;
        $this->pdf = $pdf;
    }


    public function build()
    {
        return $this
            ->subject("Hola " . strtoupper($this->work->nombres) . ", tu boleta {$this->cronograma->mes} del {$this->cronograma->año} ya está lista.")
            ->view('mails.send_boleta')
            ->attachData($this->pdf, 'boleta_informativa', [
                'mime' => 'application/pdf'
            ]);
    }

}
