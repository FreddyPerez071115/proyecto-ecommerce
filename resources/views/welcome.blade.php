<!DOCTYPE html>
<html>

<head>
    <title>Empresa E-commerce</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
    <header>
        <h1>Bienvenido a nuestra Empresa</h1>
        <nav>
            <a href="{{ route('home') }}">Inicio</a>
            <a href="#">Quiénes Somos</a>
            <a href="#">Contáctanos</a>

            @guest
            <a href="{{ route('login.form') }}">Iniciar Sesión</a>
            <a href="{{ route('register.form') }}">Registrarse</a>
            @endguest

            @auth
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit">Cerrar Sesión</button>
            </form>
            @endauth
        </nav>
    </header>

    <section>
        <p>Información sobre la empresa, misión, visión, etc.</p>
    </section>
</body>

</html>