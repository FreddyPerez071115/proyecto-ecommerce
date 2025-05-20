<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    /** @use HasFactory<\Database\Factories\ProductoFactory> */
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'precio', 'stock', 'usuario_id'];

    // Imágenes del producto (nueva relación)
    public function imagenes()
    {
        return $this->hasMany(ProductoImagen::class);
    }

    // Método para obtener la URL correcta de la imagen principal
    public function imagenPrincipalUrl()
    {
        if ($this->imagenes->isEmpty()) {
            return asset('img/no-image.png');
        }

        $rutaImagen = $this->imagenes->first()->ruta_imagen;

        // Si ya es una URL completa, devolverla tal cual
        if (strpos($rutaImagen, 'http') === 0) {
            return $rutaImagen;
        }

        // Si no, construir la URL con asset()
        return asset('storage/' . $rutaImagen);
    }

    // Categorías a las que pertenece este producto
    public function categorias()
    {
        return $this->belongsToMany(Categoria::class);
    }

    // Órdenes que incluyen este producto (muchos a muchos)
    public function ordenes()
    {
        return $this->belongsToMany(Orden::class, 'producto_orden')
            ->withPivot('cantidad', 'precio_unitario');
    }

    // Método para verificar si el vendedor es cliente
    public function esVendedorCliente()
    {
        return $this->usuario && $this->usuario->role === 'cliente';
    }

    // Imagen principal (para compatibilidad)
    public function imagenPrincipal()
    {
        return $this->imagenes()->first();
    }

    // Usuario que publicó este producto (vendedor)
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
