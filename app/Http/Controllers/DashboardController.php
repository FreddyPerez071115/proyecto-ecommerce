<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Redirigir a diferentes vistas según el rol
        if ($user->role == 'cliente') {
            return view('dashboard.cliente');
        } elseif ($user->role == 'empleado') {
            return view('dashboard.empleado');
        } elseif ($user->role == 'gerente') {
            return view('dashboard.gerente');
        }

        abort(403, 'Rol no autorizado');
    }
}
