<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class InicioController extends Controller
{
    // Mostrar formulario de login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proceso de login
    public function login(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string', // Cambiado de correo a nombre
            'clave'  => 'required',
        ]);

        // Buscar el usuario por el campo 'nombre'
        $user = \App\Models\Usuario::where('nombre', $request->nombre)->first();

        // Si se encuentra el usuario y la contraseña es correcta (usando Hash::check)
        if ($user && \Illuminate\Support\Facades\Hash::check($request->clave, $user->clave)) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'nombre' => 'Las credenciales no coinciden con nuestros registros.', // Mensaje de error asociado a nombre
        ])->withInput($request->only('nombre'));
    }

    // Mostrar formulario de registro
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Proceso de registro
    public function register(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:usuarios,nombre', // Asegurar que el nombre sea único
            'correo' => 'required|email|unique:usuarios,correo',
            'clave'  => 'required|string|min:5|confirmed', // Cambiado min:6 a min:5
            'role'   => 'required|in:cliente,administrador,gerente', // Mantener el rol si es necesario en el registro directo
        ]);

        // Por defecto, los usuarios que se registran son clientes
        //$esComprador = $data['role'] === 'cliente';
        //$esVendedor = $data['role'] === 'cliente';


        $user = Usuario::create([
            'nombre' => $data['nombre'],
            'correo' => $data['correo'],
            'clave'  => Hash::make($data['clave']),
            'role'   => $data['role'], // O forzar a 'cliente' si el registro es solo para clientes
            //'es_comprador' => $esComprador,
            //'es_vendedor' => $esVendedor,
        ]);

        Auth::login($user);
        return redirect()->route('dashboard')->with('success', '¡Registro exitoso! Bienvenido.');
    }

    // Proceso de logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
