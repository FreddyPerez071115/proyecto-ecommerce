@extends('layouts.app')

@section('content')
<h2>Registro de Usuario</h2>
<form method="POST" action="{{ route('register') }}">
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
        <option value="administrador">Administrador</option>
        <option value="gerente">Gerente</option>
    </select>
    <br>
    <button type="submit">Registrarse</button>
</form>
@endsection