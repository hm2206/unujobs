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

    public $work;
    public $cronograma;
    public $pdf;
    public $jefe;

    /**
     * @param \App\Models\Work $work
     * @param string $year
     * @param string $mes
     * @param string $adicional
     * @param \PDF $pdf
     */
    public function __construct($work, $cronograma, $pdf)
    {
        $this->work = $work;
        $this->cronograma = $cronograma;
        $this->pdf = $pdf;
        $this->jefe = Work::where("jefe", 1)->first();
    }


    public function build()
    {
        return $this->from()
            ->subject("Hola " . strtoupper($this->work->nombres) . ", tu boleta {$this->cronograma->mes} de {$this->cronograma->year} ya está lista.")
            ->view('mails.send_boleta')
            ->attachData($this->pdf->output(), 'boleta', [
                'mime' => 'application/pdf'
            ]);
    }

}
