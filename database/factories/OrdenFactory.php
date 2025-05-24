<?php

namespace Database\Factories;

use App\Models\Orden;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Orden>
 */
class OrdenFactory extends Factory
{
    protected $model = Orden::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'usuario_id' => Usuario::factory()->comprador(),
            'total' => $this->faker->randomFloat(2, 20, 5000),
            'estado' => $this->faker->randomElement([
                Orden::ESTADO_PENDIENTE, 
                Orden::ESTADO_VALIDADA,
                Orden::ESTADO_PAGADO,
                Orden::ESTADO_ENVIADO,
                Orden::ESTADO_ENTREGADO,
                Orden::ESTADO_CANCELADO
            ]),
        ];
    }

    /**
     * Indica que la orden está pendiente
     */
    public function pendiente(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => Orden::ESTADO_PENDIENTE,
        ]);
    }

    /**
     * Indica que la orden está validada
     */
    public function validada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => Orden::ESTADO_VALIDADA,
        ]);
    }
}
