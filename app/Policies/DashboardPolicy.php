<?php

namespace App\Policies;

use App\Models\Usuario;

class DashboardPolicy
{
    /**
     * Determina si el usuario puede acceder al dashboard de administración
     */
    public function viewDashboard(Usuario $usuario): bool
    {
        // Solo el administrador puede ver el dashboard con estadísticas generales
        return $usuario->role === 'administrador';
    }

    /**
     * Determina si el usuario puede ver estadísticas específicas
     * (útil si quieres dar acceso parcial al gerente)
     */
    public function viewSalesStatistics(Usuario $usuario): bool
    {
        return in_array($usuario->role, ['administrador', 'gerente']);
    }

    /**
     * Determina si el usuario puede exportar datos del dashboard
     */
    public function exportDashboardData(Usuario $usuario): bool
    {
        return $usuario->role === 'administrador';
    }
}
