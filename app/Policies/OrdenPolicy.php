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
        // Solo administradores y gerentes pueden ver el listado completo de órdenes
        return in_array($usuario->role, ['cliente', 'gerente']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Usuario $usuario, Orden $orden): bool
    {
        // El cliente solo puede ver sus propias órdenes
        if ($usuario->role === 'cliente') {
            return $orden->usuario_id === $usuario->id;
        }

        // Administradores y gerentes pueden ver todas
        return in_array($usuario->role, ['administrador', 'gerente']);
    }

    /**
     * Determine whether the user can view the ticket.
     */
    public function viewTicket(Usuario $usuario, Orden $orden): bool
    {
        // El cliente solo puede ver el ticket de sus propias órdenes
        if ($usuario->role === 'cliente') {
            return $orden->usuario_id === $usuario->id;
        }

        // Administradores y gerentes pueden ver todos los tickets
        return in_array($usuario->role, ['administrador', 'gerente']);
    }

    /**
     * Determine whether the user can view all tickets.
     */
    public function viewAllTickets(Usuario $usuario): bool
    {
        // Solo gerentes pueden ver la página de todos los tickets
        return $usuario->role === 'gerente';
    }

    /**
     * Determine whether the user can validate orders.
     */
    public function validateOrder(Usuario $usuario, Orden $orden): bool
    {
        // Solo gerentes pueden validar órdenes
        return $usuario->role === 'gerente';
    }
}
