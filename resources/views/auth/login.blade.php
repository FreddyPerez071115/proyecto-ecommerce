@extends('layouts.app')

@section('content')
<h2>Iniciar Sesión</h2>
<form method="POST" action="{{ route('login') }}">
    @csrf
    <label>Email:</label>
    <input type="email" name="email" required>
    <br>
    <label>Contraseña:</label>
    <input type="password" name="password" required>
    <br>
    <button type="submit">Ingresar</button>
</form>
@endsection