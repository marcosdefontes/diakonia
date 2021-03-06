<?php

namespace App\Mail\musica;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EscalaPublicadaColaborador extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $escala;
    protected $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($escala, $user)
    {
        $this->onQueue('emails');
        $this->escala = $escala;
        $this->user = $user;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Escala de música publicada')
            ->view('emails.musica.escala-publicada-colaboradores')
                ->with([
                    'escala' => $this->escala,
                    'user' => $this->user
                ]);
    }
}
