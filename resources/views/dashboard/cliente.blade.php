@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h2 class="fs-4 fw-bold m-0">Dashboard Cliente</h2>
                    <span class="badge bg-primary">{{ Auth::user()->nombre }}</span>
                </div>
                <div class="card-body">
                    <p class="text-muted">Bienvenido a tu panel de control. Aquí puedes gestionar tus compras, ventas y productos.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Tarjeta de estadísticas rápidas -->
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h3 class="fs-5 fw-bold m-0">Estadísticas</h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="p-3 border rounded mb-3 mb-md-0">
                                <div class="display-4 fw-bold text-primary">{{ $estadisticas['compras'] ?? 0 }}</div>
                                <div class="text-muted">Compras realizadas</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded mb-3 mb-md-0">
                                <div class="display-4 fw-bold text-success">{{ $estadisticas['productos'] ?? 0 }}</div>
                                <div class="text-muted">Productos en venta</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded">
                                <div class="display-4 fw-bold text-info">{{ $estadisticas['ventas'] ?? 0 }}</div>
                                <div class="text-muted">Ventas realizadas</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Mis órdenes recientes -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h3 class="fs-5 fw-bold m-0">Mis compras recientes</h3>
                    <a href="{{ route('ordenes.index') }}" class="btn btn-sm btn-outline-primary">Ver todas</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover m-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ordenes as $orden)
                                <tr>
                                    <td>#{{ $orden->id }}</td>
                                    <td>{{ $orden->created_at->format('d/m/Y') }}</td>
                                    <td>${{ number_format($orden->total, 2) }}</td>
                                    <td>
                                        @php
                                        $badgeClass = match($orden->estado) {
                                        'pendiente' => 'bg-warning',
                                        'validada' => 'bg-success',
                                        'pagado' => 'bg-info',
                                        'enviado' => 'bg-primary',
                                        'entregado' => 'bg-dark',
                                        'cancelado' => 'bg-danger',
                                        default => 'bg-secondary'
                                        };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ ucfirst($orden->estado) }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('ordenes.show', $orden) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-basket fs-4 d-block mb-2"></i>
                                            No has realizado compras todavía
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mis productos en venta -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h3 class="fs-5 fw-bold m-0">Mis productos</h3>
                    <a href="{{ route('productos.create') }}" class="btn btn-sm btn-success">
                        <i class="bi bi-plus"></i> Nuevo producto
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover m-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Ventas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productos as $producto)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($producto->imagenes->isNotEmpty())
                                            @php
                                            $rutaImagen = $producto->imagenes->first()->ruta_imagen;
                                            $esUrl = strpos($rutaImagen, 'http') === 0;
                                            $imagenUrl = $esUrl ? $rutaImagen : asset('storage/'.$rutaImagen);
                                            @endphp
                                            <img src="{{ $imagenUrl }}" alt="{{ $producto->nombre }}"
                                                class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                            <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                            @endif
                                            <span>{{ Str::limit($producto->nombre, 25) }}</span>
                                        </div>
                                    </td>
                                    <td>${{ number_format($producto->precio, 2) }}</td>
                                    <td>
                                        @if($producto->stock > 0)
                                        <span class="badge bg-success">{{ $producto->stock }}</span>
                                        @else
                                        <span class="badge bg-danger">Agotado</span>
                                        @endif
                                    </td>
                                    <td>{{ $producto->total_ventas ?? 0 }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('productos.show', $producto) }}" class="btn btn-outline-secondary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('productos.edit', $producto) }}" class="btn btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-box-seam fs-4 d-block mb-2"></i>
                                            No tienes productos a la venta
                                            <p class="mt-2">
                                                <a href="{{ route('productos.create') }}" class="btn btn-sm btn-outline-success">
                                                    <i class="bi bi-plus"></i> Crear mi primer producto
                                                </a>
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de ventas -->
    @if(isset($ventasPorMes) && count($ventasPorMes) > 0)
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h3 class="fs-5 fw-bold m-0">Ventas por mes</h3>
                </div>
                <div class="card-body">
                    <canvas id="ventasChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@if(isset($ventasPorMes) && count($ventasPorMes) > 0)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('ventasChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {
                    !!json_encode($ventasPorMes['labels']) !!
                },
                datasets: [{
                    label: 'Ventas por mes',
                    data: {
                        !!json_encode($ventasPorMes['data']) !!
                    },
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Ventas: $' + context.parsed.y;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endif
@endsection