@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Dashboard Gerente</h1>

    <!-- Herramientas de administración -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h2 class="fs-4 fw-bold m-0">Herramientas de Gestión</h2>
                    <a href="{{ route('ordenes.all-tickets') }}" class="btn btn-sm btn-info">
                        <i class="bi bi-file-earmark-text"></i> Ver Todos los Comprobantes
                    </a>
                </div>
                <div class="card-body">
                    <p>Acceso a funciones de gestión:</p>
                    <div class="d-flex flex-wrap gap-2"> {{-- flex-wrap para mejor responsividad --}}
                        <a href="{{ route('users.index') }}" class="btn btn-primary">
                            <i class="bi bi-people"></i> Administrar Usuarios
                        </a>
                        <a href="{{ route('ordenes.index') }}" class="btn btn-success">
                            <i class="bi bi-cart-check"></i> Gestionar Órdenes
                        </a>
                        <a href="{{ route('categorias.index') }}" class="btn btn-info">
                            <i class="bi bi-tags"></i> Ver Categorías
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas de ventas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h2 class="fs-4 fw-bold m-0">Estadísticas de Ventas</h2>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="p-3 border rounded mb-3">
                                <h3 class="fs-2">{{ $stats['ventas_pendientes'] }}</h3>
                                <p class="mb-0 text-muted">Ventas Pendientes</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded mb-3 bg-success-subtle">
                                <h3 class="fs-2">{{ $stats['ventas_validadas'] }}</h3>
                                <p class="mb-0 text-muted">Ventas Validadas</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded mb-3">
                                <h3 class="fs-2">{{ $stats['ventas_mes_actual'] }}</h3>
                                <p class="mb-0 text-muted">Ventas Este Mes</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Órdenes pendientes de validación -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h2 class="fs-4 fw-bold m-0">Órdenes Pendientes de Validación</h2>
            <span class="badge bg-warning rounded-pill">{{ count($ordenesPendientes) }}</span>
        </div>

        @if(count($ordenesPendientes) > 0)
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Comprobante</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ordenesPendientes as $orden)
                        <tr>
                            <td>#{{ $orden->id }}</td>
                            <td>{{ $orden->usuario->nombre }}</td>
                            <td>{{ $orden->created_at->format('d/m/Y') }}</td>
                            <td>${{ number_format($orden->total, 2) }}</td>
                            <td>
                                @if($orden->ticket_path)
                                <a href="{{ route('ordenes.ticket', $orden) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                                @else
                                <span class="badge bg-danger">Sin comprobante</span>
                                @endif
                            </td>
                            <td class="d-flex gap-1">
                                <a href="{{ route('ordenes.show', $orden) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-info-circle"></i> Detalles
                                </a>
                                <form action="{{ route('ordenes.validate', $orden) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success"
                                        onclick="return confirm('¿Estás seguro de validar esta orden?')">
                                        <i class="bi bi-check-circle"></i> Validar
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="card-body">
            <p class="mb-0 text-center py-3">No hay órdenes pendientes de validación.</p>
        </div>
        @endif
    </div>

    <!-- Últimas órdenes validadas -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h2 class="fs-4 fw-bold m-0">Últimas Órdenes Validadas</h2>
        </div>

        @if(isset($ordenesValidadas) && count($ordenesValidadas) > 0)
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ordenesValidadas as $orden)
                        <tr>
                            <td>#{{ $orden->id }}</td>
                            <td>{{ $orden->usuario->nombre }}</td>
                            <td>{{ $orden->created_at->format('d/m/Y') }}</td>
                            <td>${{ number_format($orden->total, 2) }}</td>
                            <td>
                                <a href="{{ route('ordenes.show', $orden) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-info-circle"></i> Detalles
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="card-body">
            <p class="mb-0 text-center py-3">No hay órdenes validadas recientemente.</p>
        </div>
        @endif
    </div>
</div>
@endsection