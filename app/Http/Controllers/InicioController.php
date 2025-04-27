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
            'correo' => 'required|email',
            'clave'  => 'required',
        ]);

        // Buscar el usuario por el campo 'correo'
        $user = \App\Models\Usuario::where('correo', $request->correo)->first();

        // Si se encuentra el usuario y la contraseña es correcta (usando Hash::check)
        if ($user && \Illuminate\Support\Facades\Hash::check($request->clave, $user->clave)) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'correo' => 'Las credenciales no coinciden con nuestros registros.',
        ]);
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
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|unique:usuarios,correo',
            'clave'  => 'required|string|min:6|confirmed',
            'role'   => 'required|in:cliente,administrador,gerente',
        ]);

        $user = Usuario::create([
            'nombre' => $data['nombre'],
            'correo' => $data['correo'],
            'clave'  => Hash::make($data['clave']),
            'role'   => $data['role'],
        ]);

        Auth::login($user);
        return redirect()->route('dashboard');
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
