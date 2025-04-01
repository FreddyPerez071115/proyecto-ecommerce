@extends('layouts.app')

@section('content')
<h2>Dashboard Cliente</h2>
<p>Opciones disponibles para clientes.</p>
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Cerrar Sesión</button>
</form>
@endsection