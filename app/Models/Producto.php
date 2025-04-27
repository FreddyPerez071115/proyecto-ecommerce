<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    /** @use HasFactory<\Database\Factories\ProductoFactory> */
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'precio', 'stock', 'usuario_id'];

    // Vendedor del producto
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    // Categorías a las que pertenece este producto (muchos a muchos)
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
}
