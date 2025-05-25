<?php

namespace App\Services;

use App\Mail\CompraValidadaParaComprador;
use App\Mail\VentaValidadaParaVendedor;
use App\Models\Orden;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificacionService
{
    /**
     * Enviar notificaciones de correo para una orden validada.
     *
     * @param Orden $orden
     * @return void
     */
    public function enviarNotificacionesOrdenValidada(Orden $orden)
    {
        try {
            // Cargar la orden con todas las relaciones necesarias
            $orden->load(['usuario', 'productos.usuario']);

            // Datos del comprador
            $comprador = $orden->usuario;

            // Agrupar los productos por vendedor para evitar enviar múltiples correos al mismo vendedor
            $productosPorVendedor = [];
            $infoVendedores = []; // Para el correo del comprador

            foreach ($orden->productos as $producto) {
                $vendedorId = $producto->usuario_id;

                if (!isset($productosPorVendedor[$vendedorId])) {
                    $productosPorVendedor[$vendedorId] = [];
                }

                $productosPorVendedor[$vendedorId][] = [
                    'producto' => $producto,
                    'cantidad' => $producto->pivot->cantidad
                ];

                // Información para el correo del comprador
                if (!isset($infoVendedores[$vendedorId])) {
                    $infoVendedores[$vendedorId] = [
                        'vendedor' => $producto->usuario,
                        'productos' => []
                    ];
                }

                $infoVendedores[$vendedorId]['productos'][] = [
                    'nombre' => $producto->nombre,
                    'cantidad' => $producto->pivot->cantidad,
                    'precio' => $producto->pivot->precio_unitario
                ];
            }

            // 1. Enviar correos a cada vendedor
            foreach ($productosPorVendedor as $vendedorId => $productos) {
                $vendedor = $productos[0]['producto']->usuario;

                // Enviar un correo por cada producto vendido
                foreach ($productos as $productoInfo) {
                    Mail::to($vendedor->correo)->send(
                        new VentaValidadaParaVendedor(
                            $orden,
                            $productoInfo['producto'],
                            $productoInfo['cantidad'],
                            $comprador
                        )
                    );
                }
            }

            // 2. Enviar correo al comprador con información de todos los vendedores
            Mail::to($comprador->correo)->send(
                new CompraValidadaParaComprador($orden, $infoVendedores)
            );

            Log::info('Notificaciones enviadas para la orden #' . $orden->id);
        } catch (\Exception $e) {
            Log::error('Error al enviar notificaciones: ' . $e->getMessage());
        }
    }
}
