@extends('layouts.app')

@php
use App\Models\Orden;
@endphp

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestión de Órdenes</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Dashboard
        </a>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h2 class="fs-5 fw-bold m-0">Filtros de búsqueda</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('ordenes.index') }}" method="GET" class="row g-3">
                <!-- Estado -->
                <div class="col-md-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        @foreach($estados as $key => $value)
                        <option value="{{ $key }}" {{ $estado == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Fecha inicio -->
                <div class="col-md-3">
                    <label for="fecha_inicio" class="form-label">Desde</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="{{ $fechaInicio }}">
                </div>

                <!-- Fecha fin -->
                <div class="col-md-3">
                    <label for="fecha_fin" class="form-label">Hasta</label>
                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="{{ $fechaFin }}">
                </div>

                <!-- Búsqueda -->
                <div class="col-md-3">
                    <label for="busqueda" class="form-label">Búsqueda</label>
                    <input type="text" class="form-control" id="busqueda" name="busqueda"
                        placeholder="ID o nombre de cliente" value="{{ $busqueda }}">
                </div>

                <!-- Botones -->
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="{{ route('ordenes.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de órdenes -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h2 class="fs-5 fw-bold m-0">Listado de Órdenes</h2>
            <span class="badge bg-primary rounded-pill">{{ $ordenes->total() }} órdenes</span>
        </div>

        @if($ordenes->count() > 0)
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Comprobante</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ordenes as $orden)
                        <tr>
                            <td>#{{ $orden->id }}</td>
                            <td>
                                <div>{{ $orden->usuario->nombre }}</div>
                                <small class="text-muted">{{ $orden->usuario->correo }}</small>
                            </td>
                            <td>{{ $orden->created_at->format('d/m/Y H:i') }}</td>
                            <td>${{ number_format($orden->total, 2) }}</td>
                            <td>
                                @php
                                $badgeClass = match($orden->estado) {
                                Orden::ESTADO_PENDIENTE => 'bg-warning',
                                Orden::ESTADO_VALIDADA => 'bg-success',
                                Orden::ESTADO_PAGADO => 'bg-info',
                                Orden::ESTADO_ENVIADO => 'bg-primary',
                                Orden::ESTADO_ENTREGADO => 'bg-dark',
                                Orden::ESTADO_CANCELADO => 'bg-danger',
                                default => 'bg-secondary'
                                };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($orden->estado) }}</span>
                            </td>
                            <td>
                                @if($orden->ticket_path)
                                <a href="{{ route('ordenes.ticket', $orden) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                                @else
                                <span class="badge bg-secondary">Sin comprobante</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('ordenes.show', $orden) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-info-circle"></i> Detalles
                                    </a>

                                    @if($orden->estado === Orden::ESTADO_PENDIENTE && $orden->ticket_path && Auth::user()->role === 'gerente')
                                    <form action="{{ route('ordenes.validate', $orden) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success"
                                            onclick="return confirm('¿Estás seguro de validar esta orden?')">
                                            <i class="bi bi-check-circle"></i> Validar
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

        <div class="card-footer bg-white">
            {{ $ordenes->withQueryString()->links() }}
        </div>
        @else
        <div class="card-body">
            <p class="text-center py-4 mb-0">No se encontraron órdenes con los criterios de búsqueda seleccionados.</p>
        </div>
        @endif
    </div>
</div>
@endsection