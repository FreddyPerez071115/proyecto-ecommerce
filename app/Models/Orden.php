<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class Orden extends Model
{
    /** @use HasFactory<\Database\Factories\OrdenFactory> */
    use HasFactory;

    protected $fillable = ['usuario_id', 'total', 'estado', 'ticket_path'];

    // Constantes para estados de la orden
    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_VALIDADA = 'validada';
    public const ESTADO_PAGADO = 'pagado';
    public const ESTADO_ENVIADO = 'enviado';
    public const ESTADO_ENTREGADO = 'entregado';
    public const ESTADO_CANCELADO = 'cancelado';

    // Accessor para obtener la URL del ticket
    public function getTicketUrlAttribute()
    {
        if ($this->ticket_path) {
            // Usar path en lugar de url
            return Storage::disk('private')->path($this->ticket_path);
        }
        return null;
    }

    // Productos en esta orden (muchos a muchos)
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_orden')
            ->withPivot('cantidad', 'precio_unitario')
            ->withTimestamps();
    }

    // Usuario que realizó esta orden (comprador)
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    // Items de la orden (uno a muchos)
    public function items()
    {
        return $this->hasMany(ProductoOrden::class, 'orden_id');
    }

    /**
     * Verifica si el comprador es un cliente
     * @return bool
     */
    public function esCompradorCliente(): bool
    {
        return $this->usuario && $this->usuario->role === 'cliente';
    }

    /**
     * Actualiza el estado de la orden a 'validada'
     * Solo un gerente puede validar una orden
     * 
     * @param Usuario $usuario El usuario que intenta validar la orden
     * @return bool Si la validación fue exitosa
     */
    public function validarOrden(Usuario $usuario): bool
    {
        // Verificar si el usuario es gerente
        if ($usuario->role !== 'gerente') {
            return false;
        }

        $this->estado = self::ESTADO_VALIDADA;
        return $this->save();
    }

    /**
     * Sube un ticket/comprobante bancario para la orden
     * 
     * @param UploadedFile $file Archivo del ticket
     * @return bool Si la subida fue exitosa
     */
    public function subirTicket(UploadedFile $file): bool
    {
        if ($file) {
            // Eliminar ticket anterior si existe
            if ($this->ticket_path) {
                Storage::disk('private')->delete($this->ticket_path);
            }

            // Guardar nuevo ticket en el disco privado
            $path = $file->store('tickets', 'private');
            $this->ticket_path = $path;
            return $this->save();
        }

        return false;
    }

    /**
     * Determina si una orden tiene un comprobante de pago subido
     * 
     * @return bool
     */
    public function tieneTicket(): bool
    {
        return !empty($this->ticket_path) &&
            Storage::disk('private')->exists($this->ticket_path);
    }
}
