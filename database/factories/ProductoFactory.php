<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\ProductoImagen;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->sentence(3),
            'descripcion' => $this->faker->paragraph(),
            'precio' => $this->faker->randomFloat(2, 10, 1000),
            'stock' => $this->faker->numberBetween(0, 100),
            'usuario_id' => Usuario::where('role', 'cliente')->inRandomOrder()->first()->id ?? 1,
        ];
    }

    /**
     * Configura el producto para tener imágenes después de crearlo
     */
    public function configure()
    {
        return $this->afterCreating(function (Producto $producto) {
            // Creamos entre 1 y 4 imágenes por producto
            $numImagenes = $this->faker->numberBetween(1, 4);

            for ($i = 0; $i < $numImagenes; $i++) {
                $esPrincipal = ($i === 0);

                $width = $this->faker->numberBetween(600, 800);
                $height = $this->faker->numberBetween(400, 600);

                $imageUrl = "https://picsum.photos/{$width}/{$height}?random=" . $this->faker->unique()->numberBetween(1, 1000);

                ProductoImagen::create([
                    'producto_id' => $producto->id,
                    'ruta_imagen' => $imageUrl
                ]);
            }
        });
    }
}
