@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h2 class="fs-4 fw-bold m-0">Editar Usuario</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" value="{{ $user->nombre }}"
                                class="form-control @error('nombre') is-invalid @enderror" required>
                            @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="correo" class="form-label">Email:</label>
                            <input type="email" id="correo" name="correo" value="{{ $user->correo }}"
                                class="form-control @error('correo') is-invalid @enderror" required>
                            @error('correo')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Rol:</label>
                            <select id="role" name="role"
                                class="form-select @error('role') is-invalid @enderror" required>
                                <option value="cliente" {{ $user->role == 'cliente' ? 'selected' : '' }}>Cliente</option>
                                <option value="administrador" {{ $user->role == 'administrador' ? 'selected' : '' }}>Administrador</option>
                                <option value="gerente" {{ $user->role == 'gerente' ? 'selected' : '' }}>Gerente</option>
                            </select>
                            @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-circle me-1"></i> Actualizar
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary px-4">
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