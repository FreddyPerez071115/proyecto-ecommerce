<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductoOrden extends Pivot
{
    /** @use HasFactory<\Database\Factories\ProductoOrdenFactory> */
    use HasFactory;

    /**
     * Indica si el modelo debe tener timestamps.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'producto_orden';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'producto_id',
        'orden_id',
        'cantidad',
        'precio_unitario'
    ];

    /**
     * Obtiene la orden a la que pertenece este item.
     */
    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }

    /**
     * Obtiene el producto asociado a este item.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * Calcula el subtotal de este item.
     *
     * @return float
     */
    public function getSubtotalAttribute()
    {
        return $this->cantidad * $this->precio_unitario;
    }
}
