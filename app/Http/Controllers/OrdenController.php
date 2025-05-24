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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    /**
     * Procesa una compra directa desde la página del producto
     */
    public function compraDirecta(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1|max:10',
        ]);

        $producto = Producto::findOrFail($request->producto_id);
        $cantidad = $request->cantidad;

        // Verificar stock
        if ($producto->stock < $cantidad) {
            return back()->with('error', 'No hay suficiente stock disponible.');
        }

        try {
            DB::beginTransaction();

            // Crear orden
            $orden = new Orden();
            $orden->usuario_id = Auth::id();
            $orden->total = $producto->precio * $cantidad;
            $orden->estado = Orden::ESTADO_PENDIENTE;
            $orden->save();

            // Agregar producto a la orden
            $orden->productos()->attach($producto->id, [
                'cantidad' => $cantidad,
                'precio_unitario' => $producto->precio,
            ]);

            // Actualizar stock
            $producto->stock -= $cantidad;
            $producto->save();

            // Generar ticket como imagen
            $ticketPath = $this->generarTicketImagen($orden, $producto, $cantidad);

            // Guardar la ruta del ticket en la orden
            if ($ticketPath) {
                $orden->ticket_path = $ticketPath;
                $orden->save();
            }

            DB::commit();

            return redirect()->route('ordenes.show', $orden)
                ->with('success', 'Tu orden ha sido creada y se ha generado un comprobante automáticamente.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error en compra directa: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al procesar tu compra: ' . $e->getMessage());
        }
    }

    /**
     * Genera una imagen de ticket para una orden
     * 
     * @param Orden $orden La orden para la que generar el ticket
     * @param Producto $producto El producto comprado
     * @param int $cantidad La cantidad de productos
     * @return string|null Ruta del archivo generado o null si falla
     */
    private function generarTicketImagen(Orden $orden, Producto $producto, int $cantidad): ?string
    {
        try {
            // Crear directorio si no existe
            Storage::disk('private')->makeDirectory('tickets');

            // Crear una imagen en blanco de 800x600 pixeles
            $im = imagecreatetruecolor(800, 600);

            // Definir colores
            $blanco = imagecolorallocate($im, 255, 255, 255);
            $negro = imagecolorallocate($im, 0, 0, 0);
            $azul = imagecolorallocate($im, 0, 0, 200);
            $gris = imagecolorallocate($im, 200, 200, 200);

            // Rellenar el fondo
            imagefilledrectangle($im, 0, 0, 800, 600, $blanco);

            // Dibujar un borde
            imagerectangle($im, 0, 0, 799, 599, $gris);

            // Título y cabecera
            $titulo = "COMPROBANTE DE COMPRA";
            $empresa = "MI ECOMMERCE S.A. DE C.V.";
            $direccion = "Av. Universidad 123, Col. Centro";
            $ciudad = "Ciudad de México, CP 45600";

            // Agregar un degradado para la cabecera
            for ($i = 0; $i < 70; $i++) {
                $color = imagecolorallocate($im, 0, 100, 180 - $i);
                imagefilledrectangle($im, 0, $i, 799, $i, $color);
            }

            // Texto en la cabecera
            $fuenteGrande = 5; // Tamaño de fuente grande
            $fuenteNormal = 4; // Tamaño de fuente normal
            $fuentePequena = 2; // Tamaño de fuente pequeña

            // Título en blanco sobre fondo azul
            imagestring($im, $fuenteGrande, 300, 20, $titulo, $blanco);
            imagestring($im, $fuenteNormal, 320, 45, $empresa, $blanco);

            // Información de la orden
            imagestring($im, $fuenteNormal, 30, 90, "ORDEN #" . $orden->id, $negro);
            imagestring($im, $fuenteNormal, 30, 110, "FECHA: " . $orden->created_at->format('d/m/Y H:i:s'), $negro);
            imagestring($im, $fuenteNormal, 30, 130, "CLIENTE: " . Auth::user()->nombre, $negro);

            // Línea separadora
            imageline($im, 30, 160, 770, 160, $gris);

            // Cabeceras de la tabla
            imagestring($im, $fuenteNormal, 30, 180, "PRODUCTO", $negro);
            imagestring($im, $fuenteNormal, 450, 180, "PRECIO", $negro);
            imagestring($im, $fuenteNormal, 550, 180, "CANTIDAD", $negro);
            imagestring($im, $fuenteNormal, 680, 180, "SUBTOTAL", $negro);

            // Línea bajo las cabeceras
            imageline($im, 30, 200, 770, 200, $gris);

            // Detalle del producto
            $y = 220;

            // Nombre del producto (con posible recorte si es muy largo)
            $nombre = strlen($producto->nombre) > 35 ? substr($producto->nombre, 0, 32) . '...' : $producto->nombre;
            imagestring($im, $fuenteNormal, 30, $y, $nombre, $negro);

            // Precio formateado
            imagestring($im, $fuenteNormal, 450, $y, "$" . number_format($producto->precio, 2), $negro);

            // Cantidad
            imagestring($im, $fuenteNormal, 550, $y, $cantidad, $negro);

            // Subtotal
            $subtotal = $producto->precio * $cantidad;
            imagestring($im, $fuenteNormal, 680, $y, "$" . number_format($subtotal, 2), $negro);

            // Línea separadora
            imageline($im, 30, $y + 30, 770, $y + 30, $gris);

            // Total
            imagestring($im, $fuenteGrande, 550, $y + 50, "TOTAL:", $negro);
            imagestring($im, $fuenteGrande, 680, $y + 50, "$" . number_format($orden->total, 2), $negro);

            // Pie de página
            $mensajePie = "Gracias por tu compra!";
            $notas = "* Este es un comprobante generado automáticamente.";
            $notas2 = "* Para cualquier aclaración, conserve este comprobante.";

            $y_pie = 500;
            imagestring($im, $fuenteNormal, 300, $y_pie, $mensajePie, $azul);
            imagestring($im, $fuentePequena, 30, $y_pie + 30, $notas, $negro);
            imagestring($im, $fuentePequena, 30, $y_pie + 50, $notas2, $negro);

            // Línea del final
            imageline($im, 0, 599, 799, 599, $gris);

            // Nombre del archivo
            $filename = 'ticket_orden_' . $orden->id . '_' . time() . '.png';
            $filepath = 'tickets/' . $filename;
            $fullPath = Storage::disk('private')->path($filepath);

            // Guardar la imagen en el disco
            imagepng($im, $fullPath);
            imagedestroy($im);

            // Devolver la ruta relativa para guardar en la BD
            return $filepath;
        } catch (\Exception $e) {
            Log::error("Error al generar ticket: " . $e->getMessage());
            return null;
        }
    }
}
