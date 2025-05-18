@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2>Iniciar Sesión</h2>
                </div>
                <div class="card-body">
                    @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

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

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary btn-block mt-2">Iniciar Sesión</button>
                        </div>

                        <div class="form-group text-center">
                            <p>¿No tienes cuenta? <a href="{{ route('register.form') }}">Regístrate aquí</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection