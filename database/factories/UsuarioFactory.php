<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Usuario>
 */
class UsuarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->name(),
            'correo' => $this->faker->unique()->safeEmail(),
            'clave' => Hash::make('password'), // contraseña por defecto para todos
            'role' => 'cliente', // por defecto son clientes
            'es_comprador' => false,
            'es_vendedor' => false
        ];
    }

    /**
     * Indica que el usuario es un comprador
     */
    public function comprador(): static
    {
        return $this->state(fn (array $attributes) => [
            'es_comprador' => true,
        ]);
    }

    /**
     * Indica que el usuario es un vendedor
     */
    public function vendedor(): static
    {
        return $this->state(fn (array $attributes) => [
            'es_vendedor' => true,
        ]);
    }

    /**
     * Indica que el usuario es un administrador
     */
    public function administrador(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'administrador',
        ]);
    }

    /**
     * Indica que el usuario es un gerente
     */
    public function gerente(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'gerente',
        ]);
    }
}
