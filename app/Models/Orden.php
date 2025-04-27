<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    /** @use HasFactory<\Database\Factories\OrdenFactory> */
    use HasFactory;

    protected $fillable = ['usuario_id', 'total', 'estado'];

    // Comprador
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    // Productos en esta orden (muchos a muchos)
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_orden')
            ->withPivot('cantidad', 'precio_unitario');
    }
}
