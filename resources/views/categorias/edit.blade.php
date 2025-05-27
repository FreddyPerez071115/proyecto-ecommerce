@extends('layouts.app')

@section('title', 'Editar Categoría: ' . $categoria->nombre)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8"> {{-- Manteniendo col-md-8 ya que las categorías pueden tener descripciones más largas --}}
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h2 class="fs-4 fw-bold m-0">Editar Categoría: {{ $categoria->nombre }}</h2>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>¡Error de Validación!</strong> Por favor, corrige los siguientes errores:
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <form action="{{ route('categorias.update', $categoria) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de la Categoría <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $categoria->nombre) }}" required autofocus>
                            @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción (Opcional)</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $categoria->descripcion) }}</textarea>
                            @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-circle me-1"></i> Actualizar Categoría
                            </button>
                            <a href="{{ route('categorias.index') }}" class="btn btn-secondary px-4">
                                <i class="bi bi-x-circle me-1"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection