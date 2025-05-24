<?php

namespace Database\Seeders;

use App\Models\Orden;
use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class OrdenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener compradores y productos
        $compradores = Usuario::where('es_comprador', true)->get();
        $productos = Producto::all();
        
        // Para cada comprador, crear entre 1 y 5 órdenes
        foreach ($compradores as $comprador) {
            $numOrdenes = rand(1, 5);
            
            for ($i = 0; $i < $numOrdenes; $i++) {
                // Crear una orden
                $orden = Orden::factory()->create([
                    'usuario_id' => $comprador->id,
                    'total' => 0, // Se calculará basado en productos
                ]);
                
                // Añadir entre 1 y 5 productos a la orden
                $ordenProductos = $productos->random(rand(1, 5));
                $total = 0;
                
                foreach ($ordenProductos as $producto) {
                    $cantidad = rand(1, 3);
                    $precioUnitario = $producto->precio;
                    
                    // Adjuntar producto a la orden con cantidad y precio unitario
                    $orden->productos()->attach($producto->id, [
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario
                    ]);
                    
                    // Sumar al total
                    $total += $cantidad * $precioUnitario;
                    
                    // Reducir stock del producto
                    $producto->stock = max(0, $producto->stock - $cantidad);
                    $producto->save();
                }
                
                // Actualizar el total de la orden
                $orden->total = $total;
                $orden->save();
            }
        }
    }
}
