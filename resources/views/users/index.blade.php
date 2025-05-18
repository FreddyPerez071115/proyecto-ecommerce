@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h2 class="card-title fs-4 fw-bold m-0">Listado de Usuarios</h2>
        </div>
        <div class="card-body">
            @if(Auth::user()->role === 'administrador')
            <div class="mb-3">
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus-fill me-2"></i>Crear Nuevo Usuario
                </a>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="25%">Nombre</th>
                            <th width="25%">Email</th>
                            <th width="15%">Rol</th>
                            <th width="30%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->nombre }}</td>
                            <td>{{ $user->correo }}</td>
                            <td>
                                @if($user->role === 'administrador')
                                <span class="badge bg-danger text-white rounded-pill px-3 py-2">
                                    <i class="bi bi-shield-lock me-1"></i>Administrador
                                </span>
                                @elseif($user->role === 'gerente')
                                <span class="badge bg-primary text-white rounded-pill px-3 py-2">
                                    <i class="bi bi-briefcase me-1"></i>Gerente
                                </span>
                                @else
                                <span class="badge bg-success text-white rounded-pill px-3 py-2">
                                    <i class="bi bi-person me-1"></i>Cliente
                                </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    @if(Auth::user()->role === 'gerente')
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i>Editar
                                    </a>

                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('¿Seguro que deseas eliminar este usuario?')">
                                            <i class="bi bi-trash me-1"></i>Eliminar
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection