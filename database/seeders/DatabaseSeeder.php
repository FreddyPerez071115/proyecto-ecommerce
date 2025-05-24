<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Llamar a seeders específicos en orden correcto
        $this->call([
            UsuarioSeeder::class,
            CategoriaSeeder::class,
            ProductoSeeder::class,
            OrdenSeeder::class,
        ]);
    }
}
