@extends('layouts.app')

@section('title', 'Gestión de Categorías')

@section('content')
<div class="container py-4 mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        {{-- El título principal se moverá al card-header para consistencia con users.index --}}
        {{-- <h1 class="fs-2 fw-bold text-primary">Categorías de Productos</h1> --}}
        @can('isGerente') {{-- Asumiendo que solo gerentes pueden crear, igual que en users.index para el botón crear --}}
        <a href="{{ route('categorias.create') }}" class="btn btn-primary ms-auto">
            <i class="bi bi-plus-circle me-2"></i>Crear Nueva Categoría
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h2 class="card-title fs-4 fw-bold m-0">Listado de Categorías</h2>
            {{-- El botón de crear podría ir aquí también si se prefiere --}}
        </div>
        <div class="card-body">
            @if($categorias->isEmpty())
            <div class="text-center p-5">
                <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                <p class="lead text-muted">No hay categorías registradas.</p>
                @can('isGerente')
                <a href="{{ route('categorias.create') }}" class="btn btn-outline-primary mt-2">
                    <i class="fas fa-plus me-1"></i> Crea la primera categoría
                </a>
                @endcan
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="10%">ID</th>
                            <th width="30%">Nombre</th>
                            <th width="35%">Descripción</th>
                            <th width="10%" class="text-center">Productos</th>
                            <th width="15%" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categorias as $categoria)
                        <tr>
                            <td>{{ $categoria->id }}</td>
                            <td>{{ $categoria->nombre }}</td>
                            <td>
                                @if($categoria->descripcion)
                                {{ Str::limit($categoria->descripcion, 60) }}
                                @else
                                <span class="text-muted fst-italic">N/A</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary rounded-pill px-2 py-1">
                                    {{ $categoria->productos_count ?? $categoria->productos()->count() }}
                                </span>
                            </td>
                            <td class="text-center">
                                @can('isGerente') {{-- Asumiendo que solo gerentes pueden editar/eliminar --}}
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i>Editar
                                    </a>
                                    <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('¿Estás seguro de que deseas eliminar esta categoría? Esta acción no se puede deshacer.')">
                                            <i class="bi bi-trash me-1"></i>Eliminar
                                        </button>
                                    </form>
                                </div>
                                @else
                                <span class="text-muted small">N/A</span>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($categorias->hasPages())
            <div class="mt-3 d-flex justify-content-center">
                {{ $categorias->links('pagination::bootstrap-5') }}
            </div>
            @endif
            @endif
        </div>
    </div>
</div>
@endsection