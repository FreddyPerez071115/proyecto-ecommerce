<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsuarioController;

// Página principal (accesible a todos)
Route::get('/', function () {
    return view('welcome'); // Vista con información de la empresa, "Quiénes somos", etc.
})->name('home');

// Rutas de autenticación
Route::get('/login', [InicioController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [InicioController::class, 'login'])->name('login');

Route::get('/register', [InicioController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [InicioController::class, 'register'])->name('register');

// Ruta de logout para usuarios autenticados
Route::post('/logout', [InicioController::class, 'logout'])->name('logout');

// Dashboard redireccionado según rol (middleware auth para restringir el acceso)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('can:isAdmin')->group(function () {
        // Solo el administrador puede crear usuarios
        Route::get('/users', [UsuarioController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UsuarioController::class, 'create'])->name('users.create');
        Route::post('/users', [UsuarioController::class, 'store'])->name('users.store');
    });
    Route::middleware('can:isGerenteOrAdmin')->group(function () {
        // Solo el gerente o administrador pueden ver la lista de usuarios
        Route::get('/users', [UsuarioController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [UsuarioController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UsuarioController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UsuarioController::class, 'destroy'])->name('users.destroy');
    });
});
