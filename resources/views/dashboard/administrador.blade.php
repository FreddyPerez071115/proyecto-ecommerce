@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Dashboard Administrativo</h1>

    <!-- Botones de administración -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h2 class="fs-4 fw-bold m-0">Herramientas de Administración</h2>
                </div>
                <div class="card-body">
                    <p>Opciones exclusivas para administradores:</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('users.index') }}" class="btn btn-primary">
                            <i class="bi bi-people"></i> Administrar Usuarios
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Estadísticas generales -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">Usuarios</div>
                <div class="card-body">
                    <h3>{{ $stats['total_usuarios'] }}</h3>
                    <div>Vendedores: {{ $stats['total_vendedores'] }}</div>
                    <div>Compradores: {{ $stats['total_compradores'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-header">Productos</div>
                <div class="card-body">
                    <h3>{{ $stats['total_productos'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-header">Órdenes</div>
                <div class="card-body">
                    <h3>{{ $stats['total_ordenes'] }}</h3>
                    <div>Pendientes: {{ $stats['ventas_pendientes'] }}</div>
                    <div>Validadas: {{ $stats['ventas_validadas'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-header">Ingresos</div>
                <div class="card-body">
                    <h3>${{ number_format($stats['ingresos_totales'], 2) }}</h3>
                    <div>Este mes: {{ $stats['ventas_mes_actual'] }} ventas</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Producto más vendido -->
    @if($productoMasVendido)
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Producto más vendido</div>
                <div class="card-body">
                    <h4>{{ $productoMasVendido->nombre }}</h4>
                    <p>Vendido {{ $productoMasVendido->total_vendido }} veces</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Productos por categoría -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Productos por Categoría</div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($productosPorCategoria as $categoria)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $categoria->nombre }}
                            <span class="badge bg-primary rounded-pill">{{ $categoria->productos_count }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Compradores frecuentes por categoría -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Compradores Frecuentes por Categoría</div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($compradoresPorCategoria as $item)
                        <li class="list-group-item">
                            <strong>{{ $item['categoria'] }}:</strong>
                            {{ $item['comprador'] }} ({{ $item['compras'] }} compras)
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos y otras estadísticas -->
    <!-- ... código para mostrar gráficos ... -->
</div>
@endsection