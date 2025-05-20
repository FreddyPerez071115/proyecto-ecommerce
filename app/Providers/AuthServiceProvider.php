<?php

namespace App\Providers;

use App\Models\Categoria;
use App\Models\Orden;
use App\Models\Producto;
use App\Models\Usuario;
use App\Policies\CategoriaPolicy;
use App\Policies\OrdenPolicy;
use App\Policies\ProductoPolicy;
use App\Policies\UsuarioPolicy;
use App\Policies\DashboardPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider; // Extiende la clase adecuada
use Illuminate\Support\Facades\Gate;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Orden::class => OrdenPolicy::class,
        Producto::class => ProductoPolicy::class,
        Categoria::class => CategoriaPolicy::class,
        Usuario::class => DashboardPolicy::class,
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Registra las políticas (si las tienes definidas)
        $this->registerPolicies();

        // Define la Gate para el rol administrador
        Gate::define('isAdmin', function ($user) {
            return $user->role === 'administrador';
        });

        // Define la Gate para el rol gerente o administrador
        Gate::define('isGerenteOrAdmin', function ($user) {
            return in_array($user->role, ['gerente', 'administrador']);
        });

        // Registrar el Dashboard policy como un Gate
        Gate::define('view-dashboard', [DashboardPolicy::class, 'viewDashboard']);
        Gate::define('view-sales-statistics', [DashboardPolicy::class, 'viewSalesStatistics']);
        Gate::define('export-dashboard-data', [DashboardPolicy::class, 'exportDashboardData']);

        // Registrar gates adicionales para verificación
        Gate::define('validate-order', [OrdenPolicy::class, 'validateOrder']);
        Gate::define('view-all-tickets', [OrdenPolicy::class, 'viewAllTickets']);
    }
}
