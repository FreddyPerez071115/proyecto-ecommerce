<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear 50 productos con imágenes
        Producto::factory(50)->create()->each(function ($producto) {
            // Asociar cada producto con 1-3 categorías aleatorias
            $categorias = Categoria::inRandomOrder()->take(rand(1, 3))->pluck('id');
            $producto->categorias()->attach($categorias);
        });
    }
}
