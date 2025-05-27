<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoriaController; // Add this line

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

    Route::middleware('can:isGerenteOrAdmin')->group(function () {
        // Solo gerente o administrador pueden acceder al dashboard
        Route::get('/users', [UsuarioController::class, 'index'])->name('users.index');
    });

    Route::middleware('can:isAdmin')->group(function () {
        // Solo el administrador puede crear usuarios
        Route::get('/users/create', [UsuarioController::class, 'create'])->name('users.create');
        Route::post('/users', [UsuarioController::class, 'store'])->name('users.store');
    });
    Route::middleware('can:isGerente')->group(function () {
        // Solo el gerente o administrador pueden ver la lista de usuarios
        Route::get('/users/{user}/edit', [UsuarioController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UsuarioController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UsuarioController::class, 'destroy'])->name('users.destroy');
    });
});

// Rutas para gestión de productos (protegidas por autenticación)
Route::middleware('auth')->group(function () {
    Route::get('/productos/create', [ProductoController::class, 'create'])->name('productos.create');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::get('/productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
    Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
});

// Ruta pública para mostrar todos los productos y un producto individual
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
Route::get('/productos/{producto}', [ProductoController::class, 'show'])->name('productos.show');

// Rutas para clientes (solo clientes pueden comprar)
Route::middleware(['auth', 'can:isCliente'])->group(function () {
    // Ruta para compra directa (solo clientes pueden comprar)
    Route::post('/ordenes/compra-directa', [OrdenController::class, 'compraDirecta'])->name('ordenes.compra-directa');
});

// Rutas para órdenes
Route::middleware(['auth'])->group(function () {
    // Listado de órdenes (con filtro en el controlador según rol)
    Route::get('/ordenes', [OrdenController::class, 'index'])->name('ordenes.index');
    // Ver detalles de una orden específica (con verificación en controlador)
    Route::get('/ordenes/{orden}', [OrdenController::class, 'show'])->name('ordenes.show');
    // Ver el comprobante/ticket de una orden (con verificación en controlador)
    Route::get('/ordenes/{orden}/ticket', [OrdenController::class, 'showTicket'])->name('ordenes.ticket');
});

// Rutas exclusivas para gerentes
Route::middleware(['auth', 'can:isGerente'])->group(function () {
    // Validar una orden (solo gerentes)
    Route::post('/ordenes/{orden}/validate', [OrdenController::class, 'validateOrder'])->name('ordenes.validate');
    // Ver todos los comprobantes pendientes (solo gerentes)
    Route::get('/tickets', [OrdenController::class, 'allTickets'])->name('ordenes.all-tickets');
});

// Rutas para gestión de Categorías (solo Gerentes/Administradores)
Route::middleware(['auth', 'can:isGerente'])->group(function () {
    Route::resource('categorias', CategoriaController::class)->except(['show']);
});
