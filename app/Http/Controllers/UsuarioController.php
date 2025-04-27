<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->role === 'gerente') {
            $users = Usuario::where('role', 'cliente')->get();
        } else {
            $users = Usuario::all();
        }

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'     => 'required|string|max:255',
            'correo'     => 'required|email|unique:usuarios,correo',
            'clave'      => 'required|string|min:6|confirmed',
            'role'       => 'required|in:cliente,administrador,gerente',
        ]);

        Usuario::create([
            'nombre' => $data['nombre'],
            'correo' => $data['correo'],
            'clave'  => Hash::make($data['clave']),
            'role'   => $data['role'],
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Usuario $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Usuario $user)
    {
        $data = $request->validate([
            'nombre'  => 'required|string|max:255',
            'correo'  => 'required|email|unique:usuarios,correo,' . $user->id,
            'role'    => 'required|in:cliente,administrador,gerente',
        ]);

        $user->update($data);
        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Usuario $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente');
    }
}
