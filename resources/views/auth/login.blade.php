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
                    @if ($errors->any() && !$errors->has('nombre') && !$errors->has('clave'))
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="nombre">Nombre de Usuario:</label>
                            <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required class="form-control @error('nombre') is-invalid @enderror" autofocus>
                            @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="clave">Contraseña:</label>
                            <input type="password" id="clave" name="clave" required class="form-control @error('clave') is-invalid @enderror">
                            @error('clave')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 text-center">
                            <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
                        </div>

                        <div class="mb-3 text-center">
                            <p>¿No tienes cuenta? <a href="{{ route('register.form') }}">Regístrate aquí</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection