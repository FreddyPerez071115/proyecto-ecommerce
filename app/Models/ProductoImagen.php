<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoImagen extends Model
{
    use HasFactory;

    protected $table = 'producto_imagens';

    protected $fillable = ['producto_id', 'ruta_imagen'];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
