@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Dashboard Administrador</h2>
        </div>
        <div class="card-body">
            <p>Opciones exclusivas para administradores:</p>

            <div>
                <a href="{{ route('users.index') }}" class="btn btn-primary">Administrar Usuarios</a>
            </div>
        </div>
    </div>
</div>
@endsection