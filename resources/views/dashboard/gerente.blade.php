@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h2 class="fs-4 fw-bold m-0">Dashboard Gerente</h2>
        </div>
        <div class="card-body">
            <p>Herramientas de administración y acceso al CRUD de usuarios.</p>

            <div>
                <a href="{{ route('users.index') }}" class="btn btn-primary">Administrar Usuarios</a>
            </div>
        </div>
    </div>
</div>
@endsection