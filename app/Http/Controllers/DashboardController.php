<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Usuario;
use App\Models\Producto;
use App\Models\Orden;
use App\Models\Categoria;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        // Verificar autenticación manualmente
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Redirigir a diferentes vistas según el rol
        if ($user->role == 'cliente') {
            return $this->clienteDashboard();
        } elseif ($user->role == 'administrador') {
            return $this->administradorDashboard();
        } elseif ($user->role == 'gerente') {
            return $this->gerenteDashboard();
        }

        abort(403, 'Rol no autorizado');
    }

    /**
     * Dashboard para clientes - muestra sus órdenes y productos vistos recientemente
     */
    protected function clienteDashboard()
    {
        $usuario = Auth::user();

        // Obtener las órdenes del cliente (solo las más recientes)
        $ordenes = Orden::where('usuario_id', $usuario->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Obtener los productos que el usuario tiene a la venta
        $productos = Producto::where('usuario_id', $usuario->id)
            ->withCount(['ordenItems as total_ventas' => function ($query) {
                $query->select(DB::raw('SUM(cantidad)'));
            }])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Estadísticas rápidas
        $estadisticas = [
            'compras' => Orden::where('usuario_id', $usuario->id)->count(),
            'productos' => Producto::where('usuario_id', $usuario->id)->count(),
            'ventas' => Producto::where('usuario_id', $usuario->id)
                ->withSum('ordenItems as total_vendidos', 'cantidad')
                ->get()
                ->sum('total_vendidos')
        ];

        // Datos para el gráfico de ventas por mes (últimos 6 meses)
        $ventasPorMes = $this->obtenerVentasPorMesCliente($usuario->id);

        return view('dashboard.cliente', compact(
            'ordenes',
            'productos',
            'estadisticas',
            'ventasPorMes'
        ));
    }

    /**
     * Obtiene los datos para el gráfico de ventas por mes
     */
    private function obtenerVentasPorMesCliente($usuarioId)
    {
        // Obtener ventas de los últimos 6 meses
        $fechaInicio = now()->subMonths(5)->startOfMonth();

        $ventasMensuales = DB::table('producto_orden')  // Usa producto_orden en lugar de orden_items
            ->join('ordens', 'producto_orden.orden_id', '=', 'ordens.id')
            ->join('productos', 'producto_orden.producto_id', '=', 'productos.id')
            ->where('productos.usuario_id', $usuarioId)
            ->where('ordens.created_at', '>=', $fechaInicio)
            ->select(
                DB::raw('YEAR(ordens.created_at) as año'),
                DB::raw('MONTH(ordens.created_at) as mes'),
                DB::raw('SUM(producto_orden.cantidad * producto_orden.precio_unitario) as total')
            )
            ->groupBy('año', 'mes')
            ->orderBy('año')
            ->orderBy('mes')
            ->get();

        // Preparar datos para el gráfico
        $labels = [];
        $data = [];

        // Crear array con todos los meses (incluso los que no tienen ventas)
        $mesesNombres = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $currentDate = clone $fechaInicio;

        for ($i = 0; $i < 6; $i++) {
            $año = $currentDate->year;
            $mes = $currentDate->month;

            // Buscar si hay ventas para este mes
            $venta = $ventasMensuales->first(function ($item) use ($año, $mes) {
                return $item->año == $año && $item->mes == $mes;
            });

            // Agregar al array de datos
            $labels[] = $mesesNombres[$mes - 1] . ' ' . $año;
            $data[] = $venta ? round($venta->total, 2) : 0;

            // Avanzar al siguiente mes
            $currentDate->addMonth();
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Dashboard para administradores - muestra todas las estadísticas
     */
    protected function administradorDashboard()
    {
        // Verificar si el usuario puede ver el dashboard administrativo
        $this->authorize('viewDashboard', Usuario::class);

        // 1. Total de usuarios registrados (usando el modelo)
        $totalUsuarios = Usuario::count();

        // 2. Total de vendedores (usuarios con productos publicados)
        $totalVendedores = Usuario::has('productos')->count();

        // 3. Total de compradores (usuarios con órdenes realizadas)
        $totalCompradores = Usuario::has('ordenes')->count();

        // 4. Productos por categoría (usando relación belongsToMany)
        $productosPorCategoria = Categoria::withCount('productos')
            ->orderByDesc('productos_count')
            ->get();

        // 5. Producto más vendido (usando relaciones)
        $productoMasVendido = $this->obtenerProductoMasVendido();

        // 6. Compradores frecuentes por categoría (usando hasManyThrough)
        $compradoresPorCategoria = $this->obtenerCompradoresPorCategoria();

        // Datos para gráficos
        $ventasPorMes = $this->obtenerVentasPorMes();
        $productosMasVendidos = $this->obtenerProductosMasVendidos();

        // Estadísticas generales (mantener las que ya tenías)
        $stats = [
            'total_usuarios' => $totalUsuarios,
            'total_vendedores' => $totalVendedores,
            'total_compradores' => $totalCompradores,
            'total_productos' => Producto::count(),
            'total_ordenes' => Orden::count(),
            'ventas_pendientes' => Orden::where('estado', Orden::ESTADO_PENDIENTE)->count(),
            'ventas_validadas' => Orden::where('estado', Orden::ESTADO_VALIDADA)->count(),
            'ventas_mes_actual' => Orden::whereMonth('created_at', now()->month)->count(),
            'ingresos_totales' => Orden::where('estado', '!=', Orden::ESTADO_CANCELADO)->sum('total'),
        ];

        return view('dashboard.administrador', compact(
            'stats',
            'productosPorCategoria',
            'productoMasVendido',
            'compradoresPorCategoria',
            'ventasPorMes',
            'productosMasVendidos'
        ));
    }

    /**
     * Dashboard para gerentes - muestra estadísticas limitadas y órdenes pendientes
     */
    protected function gerenteDashboard()
    {
        // Los gerentes pueden ver algunas estadísticas, pero no todas como el admin
        $this->authorize('viewSalesStatistics', Usuario::class);

        // Estadísticas relevantes para gerentes
        $stats = [
            'ventas_pendientes' => Orden::where('estado', Orden::ESTADO_PENDIENTE)->count(),
            'ventas_validadas' => Orden::where('estado', Orden::ESTADO_VALIDADA)->count(),
            'ventas_mes_actual' => Orden::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        // Órdenes pendientes que requieren validación
        $ordenesPendientes = Orden::where('estado', Orden::ESTADO_PENDIENTE)
            ->whereNotNull('ticket_path')  // Solo las que tienen comprobante
            ->with('usuario')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Últimas órdenes validadas
        $ordenesValidadas = Orden::where('estado', Orden::ESTADO_VALIDADA)
            ->with('usuario')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.gerente', compact('stats', 'ordenesPendientes', 'ordenesValidadas'));
    }

    /**
     * Obtener datos de ventas por mes para gráficos
     */
    protected function obtenerVentasPorMes()
    {
        // Implementar lógica para obtener ventas mensuales
        // Esta es una implementación básica, puedes mejorarla según tus necesidades
        $ventasPorMes = [];

        for ($i = 0; $i < 6; $i++) {
            $month = now()->subMonths($i);
            $count = Orden::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();

            $ventasPorMes[$month->format('M')] = $count;
        }

        // Invertir para mostrar en orden cronológico
        return array_reverse($ventasPorMes);
    }

    /**
     * Obtener los productos más vendidos para gráficos
     */
    protected function obtenerProductosMasVendidos()
    {
        // Esta es una consulta avanzada que suma las cantidades vendidas por producto
        $productos = Producto::join('producto_orden', 'productos.id', '=', 'producto_orden.producto_id')
            ->join('ordens', 'ordens.id', '=', 'producto_orden.orden_id')
            ->selectRaw('productos.id, productos.nombre, SUM(producto_orden.cantidad) as total_vendido')
            ->where('ordens.estado', '!=', Orden::ESTADO_CANCELADO)
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->get();

        return $productos;
    }

    /**
     * Exporta los datos del dashboard (solo para administradores)
     */
    public function exportarEstadisticas()
    {
        $this->authorize('exportDashboardData', Usuario::class);

        // Implementa la lógica para exportar datos
        // Puedes usar una librería como maatwebsite/excel para esto

        return redirect()->back()->with('success', 'Exportación iniciada, recibirás un correo cuando esté lista');
    }

    /**
     * Obtiene el producto más vendido usando relaciones Eloquent
     */
    protected function obtenerProductoMasVendido()
    {
        return Producto::withCount(['ordenes as total_vendido' => function ($query) {
            // Filtrar directamente en la tabla de órdenes, ya que $query ya está en esa relación
            $query->where('estado', '!=', Orden::ESTADO_CANCELADO);
        }])
            ->orderByDesc('total_vendido')
            ->first();
    }

    /**
     * Obtiene los compradores más frecuentes por categoría usando hasManyThrough
     */
    protected function obtenerCompradoresPorCategoria()
    {
        $resultado = [];

        $categorias = Categoria::all();
        foreach ($categorias as $categoria) {
            // Consulta avanzada para encontrar compradores frecuentes por categoría
            $compradores = Usuario::whereHas('ordenes.productos.categorias', function ($query) use ($categoria) {
                $query->where('categorias.id', $categoria->id);
            })
                ->withCount(['ordenes' => function ($query) use ($categoria) {
                    $query->whereHas('productos.categorias', function ($q) use ($categoria) {
                        $q->where('categorias.id', $categoria->id);
                    });
                }])
                ->orderByDesc('ordenes_count')
                ->first();

            // Agregar a los resultados
            if ($compradores) {
                $resultado[] = [
                    'categoria' => $categoria->nombre,
                    'comprador' => $compradores->nombre,
                    'compras' => $compradores->ordenes_count
                ];
            }
        }

        return collect($resultado);
    }
}
