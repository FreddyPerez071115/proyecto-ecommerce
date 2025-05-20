<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class OrdenController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        // Aplicar middleware de autenticación a todos los métodos
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // El usuario común solo ve sus órdenes, admin/gerente ven todas
        $usuario = Auth::user();

        if (in_array($usuario->role, ['administrador', 'gerente'])) {
            $ordenes = Orden::with('usuario')->orderBy('created_at', 'desc')->paginate(10);
        } else {
            $ordenes = Orden::where('usuario_id', $usuario->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('ordenes.index', compact('ordenes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Este método probablemente no se use directamente ya que las órdenes
        // se crean generalmente desde el carrito de compras
        return redirect()->route('cart.index')
            ->with('info', 'Las órdenes se crean desde el carrito de compras');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de datos
        $request->validate([
            'total' => 'required|numeric|min:0',
            'productos' => 'required|array',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        // Crear la orden
        $orden = Orden::create([
            'usuario_id' => Auth::id(),
            'total' => $request->total,
            'estado' => Orden::ESTADO_PENDIENTE
        ]);

        // Guardar los productos relacionados con sus cantidades y precios
        foreach ($request->productos as $item) {
            $producto = Producto::findOrFail($item['id']);
            $orden->productos()->attach($producto->id, [
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $producto->precio
            ]);

            // Actualizar el stock del producto
            $producto->stock -= $item['cantidad'];
            $producto->save();
        }

        return redirect()->route('ordenes.show', $orden)
            ->with('success', 'Orden creada correctamente. Por favor, sube tu comprobante de pago.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Orden $orden)
    {
        // Verificar si el usuario puede ver esta orden
        $this->authorize('view', $orden);

        // Cargar relaciones necesarias
        $orden->load(['usuario', 'productos']);

        return view('ordenes.show', compact('orden'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Orden $orden)
    {
        // Verificar si el usuario puede editar esta orden
        $this->authorize('update', $orden);

        return view('ordenes.edit', compact('orden'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Orden $orden)
    {
        // Verificar si el usuario puede actualizar esta orden
        $this->authorize('update', $orden);

        $request->validate([
            'estado' => 'sometimes|in:pendiente,validada,pagado,enviado,entregado,cancelado',
            'ticket' => 'sometimes|file|image|max:2048',
        ]);

        // Actualizar datos básicos
        if ($request->has('estado')) {
            $orden->estado = $request->estado;
        }

        // Subir comprobante de pago si se proporciona
        if ($request->hasFile('ticket')) {
            $this->subirTicket($orden, $request->file('ticket'));
        }

        $orden->save();

        return redirect()->route('ordenes.show', $orden)
            ->with('success', 'Orden actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Orden $orden)
    {
        // Verificar si el usuario puede eliminar esta orden
        $this->authorize('delete', $orden);

        // Solo permitir eliminación si está pendiente
        if ($orden->estado !== Orden::ESTADO_PENDIENTE) {
            return redirect()->route('ordenes.show', $orden)
                ->with('error', 'No se puede eliminar una orden que no esté pendiente');
        }

        // Restaurar stock de productos
        foreach ($orden->productos as $producto) {
            $cantidad = $producto->pivot->cantidad;
            $producto->stock += $cantidad;
            $producto->save();
        }

        // Eliminar ticket si existe
        if ($orden->ticket_path && Storage::disk('private')->exists($orden->ticket_path)) {
            Storage::disk('private')->delete($orden->ticket_path);
        }

        $orden->delete();

        return redirect()->route('ordenes.index')
            ->with('success', 'Orden eliminada correctamente');
    }

    /**
     * Show the ticket/voucher for an order
     */
    public function showTicket(Orden $orden)
    {
        // Verifica si el usuario puede ver este ticket específico
        $this->authorize('viewTicket', $orden);

        // Verificar que existe un ticket
        if (!$orden->ticket_path || !Storage::disk('private')->exists($orden->ticket_path)) {
            return redirect()->route('ordenes.show', $orden)
                ->with('error', 'No hay comprobante de pago para esta orden');
        }

        // Servir el archivo
        return Storage::disk('private');
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

        $orden->estado = Orden::ESTADO_VALIDADA;
        $orden->save();

        return redirect()->route('ordenes.show', $orden)
            ->with('success', 'Orden validada correctamente');
    }

    /**
     * Upload a ticket/voucher for an order
     */
    public function uploadTicket(Request $request, Orden $orden)
    {
        // Verificar si el usuario puede actualizar esta orden
        $this->authorize('update', $orden);

        $request->validate([
            'ticket' => 'required|file|image|max:2048',
        ]);

        if ($this->subirTicket($orden, $request->file('ticket'))) {
            return redirect()->route('ordenes.show', $orden)
                ->with('success', 'Comprobante de pago subido correctamente');
        }

        return redirect()->route('ordenes.show', $orden)
            ->with('error', 'No se pudo subir el comprobante de pago');
    }

    /**
     * Helper method to upload ticket
     */
    private function subirTicket(Orden $orden, $file)
    {
        if ($file) {
            // Eliminar ticket anterior si existe
            if ($orden->ticket_path && Storage::disk('private')->exists($orden->ticket_path)) {
                Storage::disk('private')->delete($orden->ticket_path);
            }

            // Guardar nuevo ticket
            $path = $file->store('tickets', 'private');
            $orden->ticket_path = $path;
            $orden->save();

            return true;
        }

        return false;
    }

    /**
     * View all tickets (only for managers)
     */
    public function allTickets()
    {
        // Verificar si el usuario puede ver todos los tickets
        $this->authorize('viewAllTickets', Orden::class);

        $ordenes = Orden::whereNotNull('ticket_path')
            ->with('usuario')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('ordenes.tickets', compact('ordenes'));
    }
}
