@extends('layouts.app')

@section('content')
<h2>Editar Usuario</h2>
<form method="POST" action="{{ route('users.update', $user) }}">
    @csrf
    @method('PUT')
    <label>Nombre:</label>
    <input type="text" name="nombre" value="{{ $user->nombre }}" required>
    <br>
    <label>Email:</label>
    <input type="email" name="correo" value="{{ $user->correo }}" required>
    <br>
    <label>Rol:</label>
    <select name="role" required>
        <option value="cliente" {{ $user->role == 'cliente' ? 'selected' : '' }}>Cliente</option>
        <option value="administrador" {{ $user->role == 'administrador' ? 'selected' : '' }}>Administrador</option>
        <option value="gerente" {{ $user->role == 'gerente' ? 'selected' : '' }}>Gerente</option>
    </select>
    <br>
    <button type="submit">Actualizar</button>
</form>
@endsection