<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    /** @use HasFactory<\Database\Factories\CategoriaFactory> */
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion'];

    // Productos en esta categoría (muchos a muchos)
    public function productos()
    {
        return $this->belongsToMany(Producto::class);
    }

    // Relación hasManyThrough para llegar a usuarios compradores a través de productos
    // Esta es la relación clave para la consulta 6
    public function compradores()
    {
        return $this->hasManyThrough(
            Usuario::class,
            Orden::class,
            'usuario_id', // Clave foránea en ordenes
            'id',         // Clave primaria en usuarios
            'id',         // Clave primaria en categorías
            'usuario_id'  // Clave local en ordenes
        );
    }
}
