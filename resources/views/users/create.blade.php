@extends('layouts.app')

@section('content')
<h2>Crear Nuevo Usuario</h2>
<form method="POST" action="{{ route('users.store') }}">
    @csrf
    <label>Nombre:</label>
    <input type="text" name="nombre" required>
    <br>
    <label>Email:</label>
    <input type="email" name="correo" required>
    <br>
    <label>Contraseña:</label>
    <input type="password" name="clave" required>
    <br>
    <label>Confirmar Contraseña:</label>
    <input type="password" name="clave_confirmation" required>
    <br>
    <label>Rol:</label>
    <select name="role" required>
        <option value="cliente">Cliente</option>
        <option value="empleado">Empleado</option>
        <option value="gerente">Gerente</option>
    </select>
    <br>
    <button type="submit">Guardar</button>
</form>
@endsection