@extends('layouts.app')

@section('content')
<div class="container-min">
    <div class="card">
        <div class="card-header">
            <h2>Editar Usuario</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="{{ $user->nombre }}" required class="form-control">
                    @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="correo">Email:</label>
                    <input type="email" id="correo" name="correo" value="{{ $user->correo }}" required class="form-control">
                    @error('correo')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="role">Rol:</label>
                    <select id="role" name="role" required class="form-control">
                        <option value="cliente" {{ $user->role == 'cliente' ? 'selected' : '' }}>Cliente</option>
                        <option value="administrador" {{ $user->role == 'administrador' ? 'selected' : '' }}>Administrador</option>
                        <option value="gerente" {{ $user->role == 'gerente' ? 'selected' : '' }}>Gerente</option>
                    </select>
                    @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary mt-2">Actualizar</button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary mt-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection