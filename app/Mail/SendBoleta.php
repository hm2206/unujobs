<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Modelo de mail para notificar al trabajador sobre su boleta
 */
class SendBoleta extends Mailable
{
    use Queueable, SerializesModels;

    public $work;
    public $year;
    public $mes;
    public $adicional;
    public $pdf;

    /**
     * @param \App\Models\Work $work
     * @param string $year
     * @param string $mes
     * @param string $adicional
     * @param \PDF $pdf
     */
    public function __construct($work, $year, $mes, $adicional, $pdf)
    {
        $this->work = $work;
        $this->year = $year;
        $this->mes = $mes;
        $this->adicional = $adicional;
        $this->pdf = $pdf;
    }


    public function build()
    {
        return $this->from('twd2206@gmail.com')
            ->subject("Hola " . strtoupper($this->work->nombres) . ", tu boleta {$this->mes} de {$this->year} ya está lista.")
            ->view('mails.send_boleta')
            ->attachData($this->pdf->output(), 'boleta', [
                'mime' => 'application/pdf'
            ]);
    }

}
