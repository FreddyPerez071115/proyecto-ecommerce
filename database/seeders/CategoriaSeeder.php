<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegurarse de que exista la categoría "Ofertas"
        Categoria::factory()->ofertas()->createOne();

        // Crear al menos otras 4 categorías para cumplir el requisito de 5
        Categoria::factory(4)->create();
    }
}
