<?php

namespace App\Policies;

use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Usuario $usuario): bool
    {
        return true; // Cualquier usuario autenticado puede ver productos
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Usuario $usuario, Producto $producto): bool
    {
        return true; // Cualquier usuario autenticado puede ver un producto
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Usuario $usuario): bool
    {
        // Solo clientes pueden crear productos para venta
        return $usuario->role === 'cliente';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Usuario $usuario, Producto $producto): bool
    {
        // Solo el propietario del producto o un gerente/admin pueden editarlo
        return $usuario->id === $producto->usuario_id ||
            in_array($usuario->role, ['gerente', 'administrador']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Usuario $usuario, Producto $producto): bool
    {
        // Solo el propietario del producto o un gerente/admin pueden eliminarlo
        return $usuario->id === $producto->usuario_id ||
            in_array($usuario->role, ['gerente', 'administrador']);
    }
}
