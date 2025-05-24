@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Comprobantes Pendientes de Validación</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Dashboard
        </a>
    </div>

    @if($ordenes->count() > 0)
    <div class="row">
        @foreach($ordenes as $orden)
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h2 class="fs-5 fw-bold m-0">Orden #{{ $orden->id }}</h2>
                </div>

                <div class="card-body">
                    <div class="mb-3 text-center">
                        <!-- Previsualización del ticket (podría ser una miniatura de la imagen) -->
                        <div class="mb-2">
                            <a href="{{ route('ordenes.ticket', $orden) }}" target="_blank" class="btn btn-light border">
                                <i class="bi bi-image fs-1 text-primary"></i>
                                <div>Ver Comprobante</div>
                            </a>
                        </div>
                    </div>

                    <div class="mb-2">
                        <strong>Cliente:</strong> {{ $orden->usuario->nombre }}
                    </div>

                    <div class="mb-2">
                        <strong>Fecha:</strong> {{ $orden->created_at->format('d/m/Y H:i') }}
                    </div>

                    <div class="mb-2">
                        <strong>Total:</strong> ${{ number_format($orden->total, 2) }}
                    </div>
                </div>

                <div class="card-footer bg-white">
                    <div class="d-flex gap-2">
                        <a href="{{ route('ordenes.show', $orden) }}" class="btn btn-outline-secondary flex-grow-1">
                            <i class="bi bi-info-circle"></i> Detalles
                        </a>

                        <form action="{{ route('ordenes.validate', $orden) }}" method="POST" class="flex-grow-1">
                            @csrf
                            <button type="submit" class="btn btn-success w-100"
                                onclick="return confirm('¿Estás seguro de validar esta orden?')">
                                <i class="bi bi-check-circle"></i> Validar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $ordenes->links() }}
    </div>
    @else
    <div class="card shadow-sm">
        <div class="card-body py-5 text-center">
            <p class="mb-0 fs-5">No hay comprobantes pendientes de validación.</p>
        </div>
    </div>
    @endif
</div>
@endsection