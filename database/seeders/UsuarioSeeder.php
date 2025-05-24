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
            'nombre' => 'Administrador',
            'correo' => 'admin@example.com',
            'clave' => Hash::make('admin123'),
            'role' => 'administrador',
        ]);

        // Crear usuario gerente
        Usuario::factory()->create([
            'nombre' => 'Gerente',
            'correo' => 'gerente@example.com',
            'clave' => Hash::make('gerente123'),
            'role' => 'gerente',
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

        // Nota: El total de clientes es 100 (70 compradores + 30 vendedores)
    }
}
