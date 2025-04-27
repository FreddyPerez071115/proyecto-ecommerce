<?php

namespace App\Policies;

use App\Models\Usuario;

class UsuarioPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return in_array($usuario->role, ['administrador', 'gerente']);
    }

    public function view(Usuario $usuario, Usuario $model): bool
    {
        return $usuario->id === $model->id ||
            in_array($usuario->role, ['administrador', 'gerente']);
    }

    public function create(Usuario $usuario): bool
    {
        return $usuario->role === 'administrador';
    }

    public function update(Usuario $usuario, Usuario $model): bool
    {
        if ($usuario->role === 'administrador') {
            return true;
        }

        if ($usuario->role === 'gerente') {
            return $model->role === 'cliente';
        }

        return $usuario->id === $model->id;
    }

    public function delete(Usuario $usuario, Usuario $model): bool
    {
        if ($usuario->role === 'administrador') {
            return true;
        }

        if ($usuario->role === 'gerente') {
            return $model->role === 'cliente';
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Usuario $usuario, Usuario $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Usuario $usuario, Usuario $model): bool
    {
        return false;
    }
}
