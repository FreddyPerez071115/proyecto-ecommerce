<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador
        Usuario::factory()->create([
            'nombre' => 'admin',
            'correo' => 'admin@admin.com',
            'clave' => Hash::make('nimda'),
            'role' => 'administrador',
        ]);

        // Crear usuario gerente
        Usuario::factory()->create([
            'nombre' => 'gerente',
            'correo' => 'gerente@gerente.com',
            'clave' => Hash::make('etnereg'),
            'role' => 'gerente',
        ]);

        // Crear usuario cliente
        Usuario::factory()->create([
            'nombre' => 'cliente',
            'correo' => 'cliente@cliente.com',
            'clave' => Hash::make('etneilc'),
            'role' => 'cliente',
        ]);

        // Crear 70 compradores
        Usuario::factory()
            ->count(70)
            ->comprador()
            ->create();

        // Crear 30 vendedores
        Usuario::factory()
            ->count(30)
            ->vendedor()
            ->create();
    }
}
