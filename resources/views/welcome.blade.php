<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiénes Somos | TechMart</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
    <header>
        <h1>Bienvenido a TechMart</h1>
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

    <section class="quienes-somos">
        <h2>Quiénes Somos</h2>
        <p>En <strong>TechMart</strong> nos apasiona ofrecerte la mejor experiencia de compra en línea. Desde nuestra fundación, hemos trabajado con un solo objetivo en mente: brindarte productos tecnológicos de alta calidad, precios competitivos y un servicio excepcional.</p>

        <h3>Nuestra Misión</h3>
        <p>Facilitar el acceso a productos tecnológicos de calidad con una experiencia de compra rápida, segura y eficiente, superando las expectativas de nuestros clientes.</p>

        <h3>Nuestra Visión</h3>
        <p>Ser la tienda en línea de referencia en tecnología, reconocida por nuestro compromiso con la excelencia, la confianza y la innovación en el comercio electrónico.</p>

        <h3>Nuetros Valores</h3>
        <ul>
            <li><strong>Calidad:</strong> Solo ofrecemos productos que garantizan satisfacción.</li>
            <li><strong>Compromiso:</strong> Atendemos a nuestros clientes con responsabilidad y transparencia.</li>
            <li><strong>Innovación:</strong> Nos adaptamos a las tendencias del mercado para ofrecer siempre lo mejor.</li>
            <li><strong>Seguridad:</strong> Protegemos tus datos y te ofrecemos métodos de pago confiables.</li>
        </ul>

        <p>En <strong>TechMart</strong>, creemos que comprar tecnología debe ser una experiencia fácil, rápida y segura. ¡Gracias por confiar en nosotros! Estamos aquí para ti.</p>
    </section>

    <footer>
        <p>&copy; 2025 TechMart. Todos los derechos reservados.</p>
    </footer>
</body>

</html>