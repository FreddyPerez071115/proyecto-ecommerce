@extends('layouts.app')

@section('content')
<h2>Registro de Usuario</h2>
<form method="POST" action="{{ route('register') }}">
    @csrf
    <label>Nombre:</label>
    <input type="text" name="name" required>
    <br>
    <label>Email:</label>
    <input type="email" name="email" required>
    <br>
    <label>Contraseña:</label>
    <input type="password" name="password" required>
    <br>
    <label>Confirmar Contraseña:</label>
    <input type="password" name="password_confirmation" required>
    <br>
    <label>Rol:</label>
    <select name="role" required>
        <option value="cliente">Cliente</option>
        <option value="empleado">Empleado</option>
        <option value="gerente">Gerente</option>
    </select>
    <br>
    <button type="submit">Registrarse</button>
</form>
@endsection