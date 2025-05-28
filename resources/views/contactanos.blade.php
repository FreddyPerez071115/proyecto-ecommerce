@extends('layouts.app')

@section('title', 'Contáctanos - TechMart')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1 class="mb-4 text-center">Contáctanos</h1>
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <p class="lead text-center">
                        ¿Tienes alguna pregunta, comentario o necesitas asistencia? ¡Estamos aquí para ayudarte!
                    </p>

                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <h4>Información de Contacto</h4>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-geo-alt-fill me-2"></i>Dirección: Calle Central, Tuxtla Gutiérrez, México</li>
                                <li><i class="bi bi-telephone-fill me-2"></i>Teléfono: +52 (961) 302-4552</li>
                                <li><i class="bi bi-envelope-fill me-2"></i>Email: <a href="mailto:soporte@techmart.com">soporte@techmart.com</a></li>
                            </ul>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h4>Horario de Atención</h4>
                            <p>Lunes a Viernes: 9:00 AM - 6:00 PM</p>
                            <p>Sábados: 10:00 AM - 2:00 PM</p>
                            <p>Domingos: Cerrado</p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <p class="text-center">
                        Para consultas directas, por favor utiliza la información de contacto proporcionada arriba.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection