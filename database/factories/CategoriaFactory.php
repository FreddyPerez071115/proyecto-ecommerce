<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Categoria;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Categoria>
 */
class CategoriaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Categoria::class;

    /**
     * Lista de posibles nombres de categorías tecnológicas
     */
    protected $categorias = [
        'Smartphones',
        'Laptops',
        'Tablets',
        'Audio',
        'Accesorios',
        'Monitores',
        'Smart Home',
        'Almacenamiento',
        'Componentes PC',
        'Periféricos',
        'Impresoras',
        'Gaming',
        'Cámaras',
        'Redes',
        'Wearables',
        'TV y Video'
    ];

    public function definition(): array
    {
        // Usa una categoría de la lista predefinida o genera una usando palabras tecnológicas
        $nombre = count($this->categorias) > 0
            ? array_shift($this->categorias)
            : $this->faker->unique()->words(2, true);

        return [
            'nombre' => $nombre,
            'descripcion' => $this->faker->sentence(),
        ];
    }

    /**
     * Indica que esta categoría es "Ofertas"
     */
    public function ofertas()
    {
        return $this->state(function (array $attributes) {
            return [
                'nombre' => 'Ofertas',
                'descripcion' => 'Productos con descuentos y promociones especiales',
            ];
        });
    }
}
