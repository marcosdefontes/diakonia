<?php

namespace App\Listeners\musica;

use App\Events\musica\TarefaEscalaAdicionada;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NotifyNovaTarefaEscalaColaborador
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  TarefaEscalaAdicionada  $event
     * @return void
     */
    public function handle(TarefaEscalaAdicionada $event)
    {
        //
    }
}
