@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mb-4">
                <div class="card-header">
                    <h2>Detalle del Producto</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Imagen del producto -->
                        <div class="col-md-5 mb-4">
                            @if($producto->imagen_path)
                            <img src="{{ asset('storage/'.$producto->imagen_path) }}" alt="{{ $producto->nombre }}" class="img-fluid rounded">
                            @else
                            <img src="{{ asset('img/no-image.png') }}" alt="Sin imagen" class="img-fluid rounded">
                            @endif
                        </div>

                        <!-- Información del producto -->
                        <div class="col-md-7">
                            <h2>{{ $producto->nombre }}</h2>
                            <h4 class="text-primary">${{ number_format($producto->precio, 2) }}</h4>

                            <p class="my-3">{{ $producto->descripcion }}</p>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div>
                                    <span class="badge bg-{{ $producto->stock > 0 ? 'success' : 'danger' }} p-2">
                                        {{ $producto->stock > 0 ? 'En stock ('.$producto->stock.')' : 'Agotado' }}
                                    </span>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a productos
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection