@extends('layouts.app')

@section('content')
<h2>Dashboard Gerente</h2>
<p>Herramientas de administración y acceso al CRUD de usuarios.</p>
<a href="{{ route('users.index') }}">Administrar Usuarios</a>
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Cerrar Sesión</button>
</form>
@endsection