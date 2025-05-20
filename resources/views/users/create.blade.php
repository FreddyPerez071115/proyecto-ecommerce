@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h2 class="fs-4 fw-bold m-0">Crear Nuevo Usuario</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}"
                                class="form-control @error('nombre') is-invalid @enderror" required>
                            @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="correo" class="form-label">Email:</label>
                            <input type="email" id="correo" name="correo" value="{{ old('correo') }}"
                                class="form-control @error('correo') is-invalid @enderror" required>
                            @error('correo')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="clave" class="form-label">Contraseña:</label>
                            <input type="password" id="clave" name="clave"
                                class="form-control @error('clave') is-invalid @enderror" required>
                            @error('clave')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="clave_confirmation" class="form-label">Confirmar Contraseña:</label>
                            <input type="password" id="clave_confirmation" name="clave_confirmation"
                                class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Rol:</label>
                            <select id="role" name="role" class="form-select" required>
                                <option value="cliente">Cliente</option>
                                <option value="administrador">Administrador</option>
                                <option value="gerente">Gerente</option>
                            </select>
                            @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 text-center">
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection