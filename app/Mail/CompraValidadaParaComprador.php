<?php

namespace App\Mail;

use App\Models\Orden;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompraValidadaParaComprador extends Mailable
{
    use Queueable, SerializesModels;

    public $orden;
    public $vendedores;

    /**
     * Create a new message instance.
     *
     * @param  Orden  $orden
     * @param  array  $vendedores
     * @return void
     */
    public function __construct(Orden $orden, array $vendedores)
    {
        $this->orden = $orden;
        $this->vendedores = $vendedores;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Tu compra ha sido validada - TechMart')
            ->markdown('emails.comprador.compra-validada');
    }
}
