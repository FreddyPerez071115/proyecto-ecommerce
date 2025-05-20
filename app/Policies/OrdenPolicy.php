<?php

namespace App\Policies;

use App\Models\Orden;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class OrdenPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Usuario $usuario): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Usuario $usuario, Orden $orden): bool
    {
        // El usuario puede ver sus propias órdenes o administradores/gerentes pueden ver cualquiera
        return $usuario->id === $orden->usuario_id ||
            in_array($usuario->role, ['administrador', 'gerente']);
    }

    /**
     * Determina si el usuario puede ver el ticket/boucher de una orden
     */
    public function viewTicket(Usuario $usuario, Orden $orden): bool
    {
        // Solo el dueño de la venta o el gerente puede visualizar el ticket
        return $usuario->id === $orden->usuario_id ||
            $usuario->role === 'gerente';
    }

    /**
     * Determina si el usuario puede ver todos los tickets/bouchers
     */
    public function viewAllTickets(Usuario $usuario): bool
    {
        // Solo el gerente puede ver todos los bouchers
        return $usuario->role === 'gerente';
    }

    /**
     * Determina si el usuario puede validar (aprobar) una venta
     */
    public function validateOrder(Usuario $usuario, Orden $orden): bool
    {
        // Solo el gerente puede validar (aprobar) una venta
        return $usuario->role === 'gerente';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Usuario $usuario): bool
    {
        return $usuario->es_comprador || in_array($usuario->role, ['administrador', 'gerente']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Usuario $usuario, Orden $orden): bool
    {
        // Sólo el administrador o gerente puede actualizar órdenes
        // También permitimos que el comprador actualice si está pendiente
        return in_array($usuario->role, ['administrador', 'gerente']) ||
            ($usuario->id === $orden->usuario_id && $orden->estado === 'pendiente');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Usuario $usuario, Orden $orden): bool
    {
        // Solo administradores o el usuario que creó la orden (si está pendiente)
        return $usuario->role === 'administrador' ||
            ($usuario->id === $orden->usuario_id && $orden->estado === 'pendiente');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Usuario $usuario, Orden $orden): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Usuario $usuario, Orden $orden): bool
    {
        return false;
    }
}
