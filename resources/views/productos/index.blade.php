<!-- filepath: resources/views/productos/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Productos</h1>

    <div class="row">
        @foreach ($productos as $producto)
        <div class="col-md-3 mb-4">
            <div class="card">
                @if($producto->imagen_path)
                <img src="{{ asset('storage/'.$producto->imagen_path) }}" class="card-img-top" alt="{{ $producto->nombre }}">
                @else
                <img src="{{ asset('img/no-image.png') }}" class="card-img-top" alt="Sin imagen">
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $producto->nombre }}</h5>
                    <p class="card-text">${{ number_format($producto->precio, 2) }}</p>
                    <a href="{{ route('productos.show', $producto->id) }}" class="btn btn-primary">Ver detalles</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{ $productos->links() }}
</div>
@endsection