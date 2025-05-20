@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Productos</h1>

    @if($productos->isEmpty())
    <div class="alert alert-info">No se encontraron productos.</div>
    @else

    <div class="row">
        @foreach ($productos as $producto)
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                @if($producto->imagenes->isNotEmpty())
                @php
                $rutaImagen = $producto->imagenes->first()->ruta_imagen;
                $esUrl = strpos($rutaImagen, 'http') === 0;
                @endphp

                @if($esUrl)
                <img src="{{ $rutaImagen }}" class="card-img-top" alt="{{ $producto->nombre }}" style="height: 200px; object-fit: cover;">
                @else
                <img src="{{ asset('storage/'.$rutaImagen) }}" class="card-img-top" alt="{{ $producto->nombre }}" style="height: 200px; object-fit: cover;">
                @endif
                @else
                <img src="{{ asset('img/no-image.png') }}" class="card-img-top" alt="Sin imagen" style="height: 200px; object-fit: contain;">
                @endif
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ \Illuminate\Support\Str::limit($producto->nombre, 50) }}</h5>
                    <p class="card-text text-primary fw-bold">${{ number_format($producto->precio, 2) }}</p>
                    <a href="{{ route('productos.show', $producto->id) }}" class="btn btn-primary mt-auto">Ver detalles</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <div class="pagination">
        {{ $productos->links() }}
    </div>
</div>
@endsection