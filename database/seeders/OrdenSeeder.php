<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Orden;
use App\Models\Producto;

class OrdenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear órdenes para compradores
        $compradores = \App\Models\Usuario::where('es_comprador', true)->get();

        foreach ($compradores as $comprador) {
            // Crear entre 1 y 5 órdenes por comprador
            $num_ordenes = rand(1, 5);

            for ($i = 0; $i < $num_ordenes; $i++) {
                // Crear una orden
                $orden = Orden::create([
                    'usuario_id' => $comprador->id,
                    'total' => 0, // Se actualizará después
                    'estado' => ['pendiente', 'pagado', 'enviado', 'entregado'][rand(0, 3)],
                ]);

                // Agregar entre 1 y 5 productos a la orden
                $productos = Producto::inRandomOrder()->take(rand(1, 5))->get();
                $total = 0;

                foreach ($productos as $producto) {
                    $cantidad = rand(1, 3);
                    $precio_unitario = $producto->precio;
                    $subtotal = $cantidad * $precio_unitario;
                    $total += $subtotal;

                    // Agregar el producto a la orden
                    $orden->productos()->attach($producto->id, [
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precio_unitario
                    ]);
                }

                // Actualizar el total de la orden
                $orden->update(['total' => $total]);
            }
        }
    }
}
