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
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider; // Extiende la clase adecuada
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Categoria::class => CategoriaPolicy::class,
        Producto::class => ProductoPolicy::class,
        Orden::class => OrdenPolicy::class,
        Usuario::class => UsuarioPolicy::class,
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
    }
}
