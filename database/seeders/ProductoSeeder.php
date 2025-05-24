<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener categorías y vendedores
        $categorias = Categoria::all();
        $vendedores = Usuario::where('es_vendedor', true)->get();

        // Para cada vendedor, crear exactamente 3 productos
        foreach ($vendedores as $vendedor) {
            // Crear exactamente 3 productos por vendedor
            for ($i = 0; $i < 3; $i++) {
                // Crear un producto para este vendedor
                $producto = Producto::factory()->create([
                    'usuario_id' => $vendedor->id
                ]);

                // Asignar entre 1 y 3 categorías a este producto
                $numCategorias = rand(1, 3);
                $productoCategorias = $categorias->random($numCategorias);
                $producto->categorias()->attach($productoCategorias->pluck('id')->toArray());
            }
        }

        $this->command->info('Se han creado 3 productos para cada vendedor.');
    }
}
