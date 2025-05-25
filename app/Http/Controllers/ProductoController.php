<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\ProductoImagen;
use App\Models\Categoria;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;

class ProductoController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Verificar que el usuario puede ver productos
        $this->authorize('viewAny', Producto::class);

        $query = Producto::with('imagenes', 'usuario', 'categorias');

        // Filtros
        if ($request->has('busqueda')) {
            $query->where('nombre', 'like', '%' . $request->busqueda . '%')
                ->orWhere('descripcion', 'like', '%' . $request->busqueda . '%');
        }

        // Si es un usuario cliente, puede ver todos los productos o filtrar solo sus productos
        if (Auth::user()->role === 'cliente' && $request->mis_productos) {
            $query->where('usuario_id', Auth::id());
        }

        // Ordenar
        $orden = $request->orden ?? 'recientes';
        switch ($orden) {
            case 'precio_asc':
                $query->orderBy('precio', 'asc');
                break;
            case 'precio_desc':
                $query->orderBy('precio', 'desc');
                break;
            case 'populares':
                $query->withCount('ordenes')->orderBy('ordenes_count', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $productos = $query->paginate(12);

        return view('productos.index', compact('productos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Verificar que el usuario puede crear productos
        $this->authorize('create', Producto::class);

        try {
            // Registrar entrada al método para depuración
            Log::info('ProductoController@create: Iniciando método');

            // Cargar categorías
            $categorias = Categoria::all();
            Log::info('ProductoController@create: Categorías cargadas: ' . $categorias->count());

            // Retornar vista
            Log::info('ProductoController@create: Renderizando vista productos.create');
            return view('productos.create', compact('categorias'));
        } catch (\Exception $e) {
            // Registrar cualquier error
            Log::error('ProductoController@create: Error: ' . $e->getMessage());

            // Redireccionar con mensaje de error
            return redirect()->route('productos.index')
                ->with('error', 'Error al cargar el formulario de creación: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Verifica permisos con authorize (que viene del trait AuthorizesRequests)
        $this->authorize('create', Producto::class);

        // Validación
        $validated = $request->validate([
            'nombre' => 'required|max:255',
            'descripcion' => 'required',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'categorias' => 'array|exists:categorias,id'
        ]);

        // Crear producto
        $producto = Producto::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'stock' => $request->stock,
            'usuario_id' => Auth::id(),
        ]);

        // Asociar categorías si existen
        if ($request->has('categorias')) {
            $producto->categorias()->attach($request->categorias);
        }

        // Subir múltiples imágenes
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $imagen) {
                $nombreArchivo = Str::random(10) . '_' . time() . '.' . $imagen->getClientOriginalExtension();
                $ruta = $imagen->storeAs('productos', $nombreArchivo, 'public');

                ProductoImagen::create([
                    'producto_id' => $producto->id,
                    'ruta_imagen' => $ruta
                ]);
            }
        }

        return redirect()->route('productos.show', $producto)
            ->with('success', 'Producto creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $producto = Producto::with(['imagenes', 'categorias', 'usuario'])
            ->findOrFail($id);

        // Verificar que el usuario puede ver este producto
        $this->authorize('view', $producto);

        // Verificar si el usuario es cliente (para mostrar o no el botón de compra)
        $puedeComprar = false;
        if (Auth::check()) {
            $puedeComprar = Auth::user()->role === 'cliente';
        }

        // Verificar si el usuario es dueño del producto o gerente (para mostrar opciones de edición)
        $puedeEditar = Auth::check() && (
            Auth::id() === $producto->usuario_id ||
            in_array(Auth::user()->role, ['gerente', 'administrador'])
        );

        return view('productos.show', compact('producto', 'puedeComprar', 'puedeEditar'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        // Verificar que el usuario puede actualizar este producto
        $this->authorize('update', $producto);

        $categorias = Categoria::all();
        $categoriasSeleccionadas = $producto->categorias->pluck('id')->toArray();

        return view('productos.edit', compact('producto', 'categorias', 'categoriasSeleccionadas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        // Verificar que el usuario puede actualizar este producto
        $this->authorize('update', $producto);

        // Validación
        $validated = $request->validate([
            'nombre' => 'required|max:255',
            'descripcion' => 'required',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'categorias' => 'array|exists:categorias,id',
            'eliminar_imagenes' => 'array'
        ]);

        // Actualizar producto
        $producto->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'stock' => $request->stock
        ]);

        // Actualizar categorías
        if ($request->has('categorias')) {
            $producto->categorias()->sync($request->categorias);
        } else {
            $producto->categorias()->detach();
        }

        // Eliminar imágenes si se solicitó
        if ($request->has('eliminar_imagenes')) {
            foreach ($request->eliminar_imagenes as $imagenId) {
                $imagen = ProductoImagen::find($imagenId);
                if ($imagen && $imagen->producto_id === $producto->id) {
                    // Eliminar archivo
                    if (Storage::disk('public')->exists($imagen->ruta_imagen)) {
                        Storage::disk('public')->delete($imagen->ruta_imagen);
                    }
                    $imagen->delete();
                }
            }
        }

        // Agregar nuevas imágenes
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $imagen) {
                $nombreArchivo = Str::random(10) . '_' . time() . '.' . $imagen->getClientOriginalExtension();
                $ruta = $imagen->storeAs('productos', $nombreArchivo, 'public');

                ProductoImagen::create([
                    'producto_id' => $producto->id,
                    'ruta_imagen' => $ruta
                ]);
            }
        }

        return redirect()->route('productos.show', $producto)
            ->with('success', 'Producto actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        // Verificar que el usuario puede eliminar este producto
        $this->authorize('delete', $producto);

        // Eliminar imágenes físicas
        foreach ($producto->imagenes as $imagen) {
            if (Storage::disk('public')->exists($imagen->ruta_imagen)) {
                Storage::disk('public')->delete($imagen->ruta_imagen);
            }
        }

        // Eliminar producto (las relaciones se borran por cascade)
        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado correctamente');
    }
}
