@extends('layouts.app')

@section('content')
<h2>Dashboard Administrador</h2>
<p>Opciones exclusivas para administradores.</p>
<a href="{{ route('users.index') }}">Administrar Usuarios</a>
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Cerrar Sesión</button>
</form>
@endsection