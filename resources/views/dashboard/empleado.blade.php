@extends('layouts.app')

@section('content')
<h2>Dashboard Empleado</h2>
<p>Opciones exclusivas para empleados.</p>
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Cerrar Sesión</button>
</form>
@endsection