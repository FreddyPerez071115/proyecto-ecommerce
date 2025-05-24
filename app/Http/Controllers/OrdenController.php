<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use App\Models\Usuario;
use App\Models\Producto;
use App\Services\NotificacionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class OrdenController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the orders with filtering options
     */
    public function index(Request $request)
    {
        // Verificar autorización - solo administradores y gerentes pueden ver todas las órdenes
        $this->authorize('viewAny', Orden::class);

        // Parámetros de filtrado
        $estado = $request->estado;
        $fechaInicio = $request->fecha_inicio;
        $fechaFin = $request->fecha_fin;
        $busqueda = $request->busqueda;

        // Consulta base
        $query = Orden::query()->with(['usuario', 'productos']);

        // Aplicar filtros si están presentes
        if ($estado) {
            $query->where('estado', $estado);
        }

        if ($fechaInicio) {
            $query->whereDate('created_at', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $query->whereDate('created_at', '<=', $fechaFin);
        }

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->whereHas('usuario', function ($userQuery) use ($busqueda) {
                    $userQuery->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('correo', 'like', "%{$busqueda}%");
                })
                    ->orWhere('id', 'like', "%{$busqueda}%");
            });
        }

        // Ordenar por fecha de creación (las más recientes primero)
        $query->orderBy('created_at', 'desc');

        // Paginar resultados
        $ordenes = $query->paginate(15);

        // Obtener estados disponibles para el filtro
        $estados = [
            Orden::ESTADO_PENDIENTE => 'Pendiente',
            Orden::ESTADO_VALIDADA => 'Validada',
            Orden::ESTADO_PAGADO => 'Pagado',
            Orden::ESTADO_ENVIADO => 'Enviado',
            Orden::ESTADO_ENTREGADO => 'Entregado',
            Orden::ESTADO_CANCELADO => 'Cancelado'
        ];

        return view('ordenes.index', compact('ordenes', 'estados', 'estado', 'fechaInicio', 'fechaFin', 'busqueda'));
    }

    /**
     * Show the details of a specific order
     */
    public function show(Orden $orden)
    {
        // Verificar autorización
        $this->authorize('view', $orden);

        // Cargar relaciones necesarias para mostrar detalles completos
        $orden->load(['usuario', 'productos.usuario', 'productos.categorias']);

        return view('ordenes.show', compact('orden'));
    }

    /**
     * Display the ticket/receipt for an order
     */
    public function showTicket(Orden $orden)
    {
        // Verificar autorización
        $this->authorize('viewTicket', $orden);

        // Verificar si la orden tiene un ticket
        if (!$orden->ticket_path) {
            return back()->with('error', 'Esta orden no tiene un comprobante adjunto.');
        }

        // Obtener el path completo del archivo
        $path = Storage::disk('private')->path($orden->ticket_path);

        // Verificar que el archivo exista
        if (!file_exists($path)) {
            return back()->with('error', 'No se pudo encontrar el archivo del comprobante.');
        }

        // Obtener mime type y mostrar la imagen
        $mimeType = mime_content_type($path);
        $content = file_get_contents($path);

        // Devolver respuesta con el contenido del archivo
        return Response::make($content, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="comprobante-orden-' . $orden->id . '"'
        ]);
    }

    /**
     * Display a page with all pending tickets for review
     */
    public function allTickets()
    {
        // Verificar autorización
        $this->authorize('viewAllTickets', Orden::class);

        // Obtener órdenes pendientes con tickets
        $ordenes = Orden::where('estado', Orden::ESTADO_PENDIENTE)
            ->whereNotNull('ticket_path')
            ->with('usuario')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('ordenes.tickets', compact('ordenes'));
    }

    /**
     * Validate an order (change state to validated)
     */
    public function validateOrder(Orden $orden)
    {
        // Verifica si el usuario puede validar órdenes
        $this->authorize('validateOrder', $orden);

        // Verificar que la orden esté en estado pendiente
        if ($orden->estado !== Orden::ESTADO_PENDIENTE) {
            return redirect()->route('ordenes.show', $orden)
                ->with('error', 'Solo se pueden validar órdenes pendientes');
        }

        // Verificar que tenga un ticket/comprobante
        if (!$orden->ticket_path) {
            return redirect()->route('ordenes.show', $orden)
                ->with('error', 'No se puede validar una orden sin comprobante de pago');
        }

        // Cambiar estado a validada
        $orden->estado = Orden::ESTADO_VALIDADA;
        $orden->save();

        return redirect()->route('ordenes.show', $orden)
            ->with('success', 'Orden validada correctamente. Se han enviado notificaciones por correo.');
    }
}
