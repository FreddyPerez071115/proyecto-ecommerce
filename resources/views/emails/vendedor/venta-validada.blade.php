@component('mail::message')
# ¡Felicitaciones por tu venta!

Querido(a) {{ $producto->usuario->nombre }},

Una de tus ventas ha sido validada en TechMart. Aquí están los detalles:

## Detalles del Producto:

@component('mail::panel')
**{{ $producto->nombre }}**
**Cantidad:** {{ $cantidad }}
**Precio unitario:** ${{ number_format($producto->pivot->precio_unitario, 2) }}
**Total:** ${{ number_format($producto->pivot->precio_unitario * $cantidad, 2) }}
@endcomponent

## Datos del Comprador:

@component('mail::panel')
**Nombre:** {{ $comprador->nombre }}
**Correo:** {{ $comprador->correo }}
**Teléfono:** {{ $comprador->telefono ?? 'No disponible' }}
@if($comprador->direccion)
**Dirección de envío:** {{ $comprador->direccion }}
@endif
@endcomponent

## Información Importante:

- Por favor contacta al comprador para coordinar los detalles del envío.
- Debes enviar el producto lo antes posible.
- Número de orden: #{{ $orden->id }}

@component('mail::button', ['url' => route('ordenes.show', $orden->id)])
Ver Detalles de la Orden
@endcomponent

¡Gracias por ser parte de nuestra comunidad de vendedores!

Saludos,
El equipo de TechMart
@endcomponent