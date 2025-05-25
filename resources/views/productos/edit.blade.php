@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Editar Producto</h1>
        <a href="{{ route('productos.show', $producto) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Producto
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('productos.update', $producto) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Producto</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required>{{ old('descripcion', $producto->descripcion) }}</textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="precio" class="form-label">Precio</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" class="form-control" id="precio" name="precio" value="{{ old('precio', $producto->precio) }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="stock" class="form-label">Stock Disponible</label>
                        <input type="number" min="0" class="form-control" id="stock" name="stock" value="{{ old('stock', $producto->stock) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="categorias" class="form-label">Categorías</label>
                    <select class="form-select" id="categorias" name="categorias[]" multiple>
                        @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}" {{ in_array($categoria->id, old('categorias', $categoriasSeleccionadas)) ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                        @endforeach
                    </select>
                    <div class="form-text">Mantén presionada la tecla Ctrl para seleccionar múltiples categorías</div>
                </div>

                @if($producto->imagenes->count() > 0)
                <div class="mb-4">
                    <label class="form-label">Imágenes Actuales</label>
                    <div class="d-flex flex-wrap gap-3 mb-2">
                        @foreach($producto->imagenes as $imagen)
                        <div class="position-relative">
                            <img src="{{ asset('storage/' . $imagen->ruta_imagen) }}" alt="Imagen del producto" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="eliminar_imagenes[]" value="{{ $imagen->id }}" id="eliminar_imagen_{{ $imagen->id }}">
                                <label class="form-check-label" for="eliminar_imagen_{{ $imagen->id }}">
                                    Eliminar
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="form-text mb-3">Marca las casillas para eliminar las imágenes seleccionadas.</div>
                </div>
                @endif

                <div class="mb-4">
                    <label for="imagenes" class="form-label">Agregar Nuevas Imágenes</label>
                    <input class="form-control" type="file" id="imagenes" name="imagenes[]" accept="image/*" multiple>
                    <div class="form-text">Puedes seleccionar múltiples imágenes. Tamaño máximo: 2MB por imagen.</div>
                    <div id="image-preview-container" class="mt-3 d-flex flex-wrap gap-2"></div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('productos.show', $producto) }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Preview de imágenes seleccionadas
    document.getElementById('imagenes').addEventListener('change', function(e) {
        const container = document.getElementById('image-preview-container');
        container.innerHTML = '';

        for (const file of this.files) {
            const reader = new FileReader();

            reader.onload = function(event) {
                const imgContainer = document.createElement('div');
                imgContainer.className = 'position-relative';

                const img = document.createElement('img');
                img.src = event.target.result;
                img.className = 'img-thumbnail';
                img.style.width = '100px';
                img.style.height = '100px';
                img.style.objectFit = 'cover';

                imgContainer.appendChild(img);
                container.appendChild(imgContainer);
            }

            reader.readAsDataURL(file);
        }
    });
</script>
@endpush