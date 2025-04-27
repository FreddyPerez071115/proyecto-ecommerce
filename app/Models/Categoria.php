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
}
