<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productos = Producto::factory()->count(50)->create();

        // Asignamos categorías aleatorias a cada producto
        $categorias = Categoria::all();

        foreach ($productos as $producto) {
            $categorias_ids = $categorias->random(rand(1, 3))->pluck('id');
            $producto->categorias()->attach($categorias_ids);
        }
    }
}
