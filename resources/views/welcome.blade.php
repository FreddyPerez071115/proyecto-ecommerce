<!DOCTYPE html>
<html>

<head>
    <title>Empresa E-commerce</title>
</head>

<body>
    <header>
        <h1>Bienvenido a nuestra Empresa</h1>
        <nav>
            <a href="{{ route('home') }}">Inicio</a>
            <a href="#">Quiénes Somos</a>
            <a href="#">Contáctanos</a>
            <a href="{{ route('login.form') }}">Iniciar Sesión</a>
            <a href="{{ route('register.form') }}">Registrarse</a>
        </nav>
    </header>
    <section>
        <p>Información sobre la empresa, misión, visión, etc.</p>
    </section>
</body>

</html>