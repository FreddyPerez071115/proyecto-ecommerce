@extends('layouts.app')

@section('title', 'Quiénes Somos | TechMart')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <section class="quienes-somos">
                <h2 class="display-5 fw-bold mb-4 text-center">Bienvenido a TechMart</h2>
                <p class="lead mb-4">En <strong>TechMart</strong> nos apasiona ofrecerte la mejor experiencia de compra en línea. Desde nuestra fundación, hemos trabajado con un solo objetivo en mente: brindarte productos tecnológicos de alta calidad, precios competitivos y un servicio excepcional.</p>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h3 class="fs-4 fw-bold mb-3 text-primary">Nuestra Misión</h3>
                        <p>Facilitar el acceso a productos tecnológicos de calidad con una experiencia de compra rápida, segura y eficiente, superando las expectativas de nuestros clientes.</p>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h3 class="fs-4 fw-bold mb-3 text-primary">Nuestra Visión</h3>
                        <p>Ser la tienda en línea de referencia en tecnología, reconocida por nuestro compromiso con la excelencia, la confianza y la innovación en el comercio electrónico.</p>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h3 class="fs-4 fw-bold mb-3 text-primary">Nuestros Valores</h3>
                        <ul>
                            <li><strong>Calidad:</strong> Solo ofrecemos productos que garantizan satisfacción.</li>
                            <li><strong>Compromiso:</strong> Atendemos a nuestros clientes con responsabilidad y transparencia.</li>
                            <li><strong>Innovación:</strong> Nos adaptamos a las tendencias del mercado para ofrecer siempre lo mejor.</li>
                            <li><strong>Seguridad:</strong> Protegemos tus datos y te ofrecemos métodos de pago confiables.</li>
                        </ul>
                    </div>
                </div>

                <p>En <strong>TechMart</strong>, creemos que comprar tecnología debe ser una experiencia fácil, rápida y segura. ¡Gracias por confiar en nosotros! Estamos aquí para ti.</p>
            </section>
        </div>
    </div>
</div>
@endsection