<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'correo',
        'clave',
        'role',
        'es_comprador',
        'es_vendedor',
    ];

    // Productos que este usuario ha publicado (como vendedor)
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    // Órdenes que este usuario ha realizado (como comprador)
    public function ordenes()
    {
        return $this->hasMany(Orden::class);
    }

    // Categorías a las que pertenecen los productos del usuario (hasManyThrough)
    public function categorias()
    {
        return $this->hasManyThrough(
            Categoria::class,
            Producto::class,
            'usuario_id', // Clave foránea en Producto
            'id', // Clave primaria en Categoria
            'id', // Clave primaria en Usuario
            'categoria_id' // Clave foránea en la tabla pivot
        );
    }
}
