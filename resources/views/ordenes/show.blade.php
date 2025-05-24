@extends('layouts.app')

@php
use App\Models\Orden;
@endphp

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Detalle de Orden #{{ $orden->id }}</h1>
        <div>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i> Volver
            </a>

            @if($orden->estado === Orden::ESTADO_PENDIENTE && $orden->ticket_path && Auth::user()->role === 'gerente')
            <form action="{{ route('ordenes.validate', $orden) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success"
                    onclick="return confirm('¿Estás seguro de validar esta orden?')">
                    <i class="bi bi-check-circle"></i> Validar Orden
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Información general de la orden -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h2 class="fs-4 fw-bold m-0">Información de la Orden</h2>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Estado:</strong>
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
                        </div>

                        <div class="col-md-6">
                            <strong>Fecha de creación:</strong> {{ $orden->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Cliente:</strong> {{ $orden->usuario->nombre }}
                        </div>
                        <div class="col-md-6">
                            <strong>Correo:</strong> {{ $orden->usuario->correo }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <strong>Total de la orden:</strong> ${{ number_format($orden->total, 2) }}
                        </div>
                        <div class="col-md-6">
                            <strong>Comprobante:</strong>
                            @if($orden->ticket_path)
                            <a href="{{ route('ordenes.ticket', $orden) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i> Ver comprobante
                            </a>
                            @else
                            <span class="text-danger">Sin comprobante</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos en la orden -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h2 class="fs-4 fw-bold m-0">Productos en la Orden</h2>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio Unitario</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                    <th>Vendedor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orden->productos as $producto)
                                <tr>
                                    <td>{{ $producto->nombre }}</td>
                                    <td>${{ number_format($producto->pivot->precio_unitario, 2) }}</td>
                                    <td>{{ $producto->pivot->cantidad }}</td>
                                    <td>${{ number_format($producto->pivot->precio_unitario * $producto->pivot->cantidad, 2) }}</td>
                                    <td>{{ $producto->usuario->nombre }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th>${{ number_format($orden->total, 2) }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel lateral con información adicional -->
        <div class="col-md-4">
            <!-- Historia de estados (si implementas seguimiento) -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h2 class="fs-5 fw-bold m-0">Estado de la Orden</h2>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Pendiente
                            @if($orden->estado == Orden::ESTADO_PENDIENTE || $orden->estado != Orden::ESTADO_CANCELADO)
                            <span class="badge bg-success rounded-pill"><i class="bi bi-check-circle"></i></span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Validada
                            @if($orden->estado == Orden::ESTADO_VALIDADA || $orden->estado == Orden::ESTADO_PAGADO || $orden->estado == Orden::ESTADO_ENVIADO || $orden->estado == Orden::ESTADO_ENTREGADO)
                            <span class="badge bg-success rounded-pill"><i class="bi bi-check-circle"></i></span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Pagada
                            @if($orden->estado == Orden::ESTADO_PAGADO || $orden->estado == Orden::ESTADO_ENVIADO || $orden->estado == Orden::ESTADO_ENTREGADO)
                            <span class="badge bg-success rounded-pill"><i class="bi bi-check-circle"></i></span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Enviada
                            @if($orden->estado == Orden::ESTADO_ENVIADO || $orden->estado == Orden::ESTADO_ENTREGADO)
                            <span class="badge bg-success rounded-pill"><i class="bi bi-check-circle"></i></span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Entregada
                            @if($orden->estado == Orden::ESTADO_ENTREGADO)
                            <span class="badge bg-success rounded-pill"><i class="bi bi-check-circle"></i></span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Acciones disponibles según el rol y estado -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h2 class="fs-5 fw-bold m-0">Acciones</h2>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(Auth::user()->role === 'gerente' && $orden->estado === Orden::ESTADO_PENDIENTE && $orden->ticket_path)
                        <form action="{{ route('ordenes.validate', $orden) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100"
                                onclick="return confirm('¿Estás seguro de validar esta orden?')">
                                <i class="bi bi-check-circle"></i> Validar Orden
                            </button>
                        </form>
                        @endif

                        @if($orden->ticket_path)
                        <a href="{{ route('ordenes.ticket', $orden) }}" target="_blank" class="btn btn-info">
                            <i class="bi bi-file-earmark-text"></i> Ver Comprobante
                        </a>
                        @endif

                        <a href="{{ route('ordenes.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-list"></i> Ver Todas las Órdenes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection