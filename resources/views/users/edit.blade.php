@extends('layouts.app')

@section('content')
<h2>Editar Usuario</h2>
<form method="POST" action="{{ route('users.update', $user) }}">
    @csrf
    @method('PUT')
    <label>Nombre:</label>
    <input type="text" name="name" value="{{ $user->name }}" required>
    <br>
    <label>Email:</label>
    <input type="email" name="email" value="{{ $user->email }}" required>
    <br>
    <label>Rol:</label>
    <select name="role" required>
        <option value="cliente" {{ $user->role == 'cliente' ? 'selected' : '' }}>Cliente</option>
        <option value="empleado" {{ $user->role == 'empleado' ? 'selected' : '' }}>Empleado</option>
        <option value="gerente" {{ $user->role == 'gerente' ? 'selected' : '' }}>Gerente</option>
    </select>
    <br>
    <button type="submit">Actualizar</button>
</form>
@endsection