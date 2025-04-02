<!DOCTYPE html>
<html>

<head>
    <title>E-commerce</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
    <header>
        <nav>
            <a href="{{ route('home') }}">Inicio</a>
            @auth
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit">Cerrar Sesión</button>
            </form>
            @else
            <a href="{{ route('login.form') }}">Iniciar Sesión</a>
            <a href="{{ route('register.form') }}">Registrarse</a>
            @endauth
        </nav>
    </header>
    <main>
        @if(session('success'))
        <p style="color:green;">{{ session('success') }}</p>
        @endif
        @yield('content')
    </main>
</body>

</html>