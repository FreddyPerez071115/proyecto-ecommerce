@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2>Registro de Usuario</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group">
                            <label for="nombre">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required class="form-control">
                            @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="correo">Email:</label>
                            <input type="email" id="correo" name="correo" value="{{ old('correo') }}" required class="form-control">
                            @error('correo')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="clave">Contraseña:</label>
                            <input type="password" id="clave" name="clave" required class="form-control">
                            @error('clave')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="clave_confirmation">Confirmar Contraseña:</label>
                            <input type="password" id="clave_confirmation" name="clave_confirmation" required class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="role">Rol:</label>
                            <select id="role" name="role" required class="form-control">
                                <option value="cliente" selected>Cliente</option>
                                <option value="administrador">Administrador</option>
                                <option value="gerente">Gerente</option>
                            </select>
                            @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary btn-block mt-2">Registrarse</button>
                        </div>

                        <div class="form-group text-center">
                            <p>¿Ya tienes cuenta? <a href="{{ route('login.form') }}">Inicia sesión aquí</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection