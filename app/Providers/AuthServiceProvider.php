<?php

namespace App\Providers;

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
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Registra las políticas (si las tienes definidas)
        $this->registerPolicies();

        // Define la Gate para el rol gerente
        Gate::define('isGerente', function ($user) {
            return $user->role === 'gerente';
        });
    }
}
