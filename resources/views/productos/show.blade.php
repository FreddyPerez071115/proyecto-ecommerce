@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h2 class="fs-4 fw-bold m-0">Detalle del Producto</h2>
                    <a href="{{ route('productos.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Volver a productos
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Imágenes del producto -->
                        <div class="col-md-5 mb-4">
                            @if($producto->imagenes->isNotEmpty())
                            @php
                            $rutaImagen = $producto->imagenes->first()->ruta_imagen;
                            $esUrl = strpos($rutaImagen, 'http') === 0;
                            @endphp

                            <!-- Imagen principal con contenedor fijo -->
                            <div class="mb-3 position-relative img-container" style="height: 300px; overflow: hidden;">
                                @if($esUrl)
                                <img src="{{ $rutaImagen }}" alt="{{ $producto->nombre }}" class="img-fluid rounded w-100 h-100 object-fit-cover" id="imagenPrincipal">
                                @else
                                <img src="{{ asset('storage/'.$rutaImagen) }}" alt="{{ $producto->nombre }}" class="img-fluid rounded w-100 h-100 object-fit-cover" id="imagenPrincipal">
                                @endif
                            </div>

                            <!-- Miniaturas de imágenes adicionales -->
                            @if($producto->imagenes->count() > 1)
                            <div class="row g-1 thumbnails-container">
                                @foreach($producto->imagenes as $index => $imagen)
                                @php
                                $rutaMiniatura = $imagen->ruta_imagen;
                                $esMiniUrl = strpos($rutaMiniatura, 'http') === 0;
                                $imagenUrl = $esMiniUrl ? $rutaMiniatura : asset('storage/'.$rutaMiniatura);
                                @endphp
                                <div class="col-3 mb-2">
                                    <img
                                        src="{{ $imagenUrl }}"
                                        class="img-thumbnail {{ $loop->first ? 'active' : '' }}"
                                        style="cursor: pointer; height: 60px; object-fit: cover;"
                                        onclick="cambiarImagen('{{ addslashes($imagenUrl) }}', this)"
                                        data-index="{{ $index }}"
                                        alt="Miniatura {{ $index + 1 }}">
                                </div>
                                @endforeach
                            </div>
                            @endif
                            @else
                            <div class="text-center p-4 bg-light rounded">
                                <img src="{{ asset('img/no-image.png') }}" alt="Sin imagen" class="img-fluid" style="max-height: 300px;">
                                <p class="text-muted mt-2">No hay imágenes disponibles para este producto</p>
                            </div>
                            @endif
                        </div>

                        <!-- Información del producto -->
                        <div class="col-md-7">
                            <h2>{{ $producto->nombre }}</h2>
                            <h4 class="text-primary">${{ number_format($producto->precio, 2) }}</h4>

                            <p class="my-3">{{ $producto->descripcion }}</p>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div>
                                    <span class="badge bg-{{ $producto->stock > 0 ? 'success' : 'danger' }} p-2 text-white">
                                        <i class="bi bi-{{ $producto->stock > 0 ? 'check-circle' : 'x-circle' }} me-1"></i>
                                        {{ $producto->stock > 0 ? 'En stock ('.$producto->stock.')' : 'Agotado' }}
                                    </span>
                                </div>


                            </div>

                            <!-- Categorías del producto -->
                            @if($producto->categorias->isNotEmpty())
                            <div class="mt-4">
                                <p class="mb-2 fw-bold">Categorías:</p>
                                <div>
                                    @foreach($producto->categorias as $categoria)
                                    <span class="badge bg-secondary me-1 mb-1">{{ $categoria->nombre }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- Vendedor -->
                            <div class="mt-4">
                                <p class="mb-1">Vendedor: <span class="fw-bold">{{ $producto->usuario->nombre }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Función mejorada para cambiar imagen y marcar la miniatura activa
    function cambiarImagen(rutaImagen, elemento) {
        document.getElementById('imagenPrincipal').src = rutaImagen;

        // Remover clase active de todas las miniaturas
        const miniaturas = document.querySelectorAll('.thumbnails-container .img-thumbnail');
        miniaturas.forEach(miniatura => miniatura.classList.remove('active'));

        // Añadir clase active a la miniatura seleccionada
        if (elemento) {
            elemento.classList.add('active');
        }
    }
</script>

<style>
    /* Estilos para mejorar la galería */
    .img-thumbnail.active {
        border-color: #0d6efd;
        box-shadow: 0 0 0 2px #0d6efd;
    }

    .object-fit-cover {
        object-fit: cover;
    }

    /* Transición suave para cambio de imágenes */
    #imagenPrincipal {
        transition: opacity 0.3s ease;
    }

    #imagenPrincipal.changing {
        opacity: 0.7;
    }
</style>
@endsection