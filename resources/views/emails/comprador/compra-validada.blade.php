@component('mail::message')
# ¡Tu compra ha sido validada!

Hola {{ $orden->usuario->nombre }},

Nos complace informarte que tu compra ha sido validada exitosamente. Aquí están los detalles:

**Número de orden:** #{{ $orden->id }}
**Fecha de validación:** {{ now()->format('d/m/Y H:i') }}
**Total:** ${{ number_format($orden->total, 2) }}

## Detalles de tus productos:

@foreach($vendedores as $vendedorInfo)
### Productos del vendedor: {{ $vendedorInfo['vendedor']->nombre }}

@component('mail::table')
| Producto | Cantidad | Precio |
|:---------|:--------:|--------:|
@foreach($vendedorInfo['productos'] as $producto)
| {{ $producto['nombre'] }} | {{ $producto['cantidad'] }} | ${{ number_format($producto['precio'], 2) }} |
@endforeach
@endcomponent

**Contacto del vendedor:**
Correo: {{ $vendedorInfo['vendedor']->correo }}
@if($vendedorInfo['vendedor']->telefono)
Teléfono: {{ $vendedorInfo['vendedor']->telefono }}
@endif

Te recomendamos ponerte en contacto con el vendedor para coordinar los detalles de entrega.

---
@endforeach

@component('mail::button', ['url' => route('ordenes.show', $orden->id)])
Ver Detalles de tu Compra
@endcomponent

Si tienes alguna pregunta o inquietud, no dudes en contactarnos.

¡Gracias por comprar en TechMart!

Saludos,
El equipo de TechMart
@endcomponent