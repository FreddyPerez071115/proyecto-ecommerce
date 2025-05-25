<?php

namespace App\Mail;

use App\Models\Orden;
use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VentaValidadaParaVendedor extends Mailable
{
    use Queueable, SerializesModels;

    public $orden;
    public $producto;
    public $cantidad;
    public $comprador;

    /**
     * Create a new message instance.
     *
     * @param  Orden  $orden
     * @param  Producto  $producto
     * @param  int  $cantidad
     * @param  Usuario  $comprador
     * @return void
     */
    public function __construct(Orden $orden, Producto $producto, int $cantidad, Usuario $comprador)
    {
        $this->orden = $orden;
        $this->producto = $producto;
        $this->cantidad = $cantidad;
        $this->comprador = $comprador;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('¡Has realizado una venta en TechMart!')
            ->markdown('emails.vendedor.venta-validada');
    }
}
