@extends('layouts.app')

@section('content')
<h2>Listado de Usuarios</h2>
@if(Auth::user()->role === 'administrador')
<a href="{{ route('users.create') }}">Crear Nuevo Usuario</a>
@endif
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->nombre }}</td>
            <td>{{ $user->correo }}</td>
            <td>{{ $user->role }}</td>
            <td>
                @if(Auth::user()->role === 'gerente')
                <a href="{{ route('users.edit', $user) }}">Editar</a>
                @endif
                @if(Auth::user()->role === 'cliente')
                <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('¿Seguro que deseas eliminar este usuario?')">Eliminar</button>
                </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection