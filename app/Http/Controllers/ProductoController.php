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
use Illuminate\Routing\Controller; // Asegúrate que sea Illuminate\Routing\Controller o tu BaseController

class ProductoController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Cualquiera puede ver el listado de productos, la autorización se aplica si es necesaria
        // $this->authorize('viewAny', Producto::class); // Descomentar si solo usuarios auth pueden ver

        $query = Producto::with('imagenes', 'usuario', 'categorias')->where('stock', '>', 0); // Mostrar solo productos con stock

        // Filtros
        if ($request->has('busqueda') && !empty($request->busqueda)) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->busqueda . '%')
                    ->orWhere('descripcion', 'like', '%' . $request->busqueda . '%');
            });
        }

        if ($request->has('categoria') && !empty($request->categoria)) {
            $query->whereHas('categorias', function ($q) use ($request) {
                $q->where('categorias.id', $request->categoria);
            });
        }

        // Si el usuario está autenticado y es un cliente, y solicita "mis productos"
        if (Auth::check() && Auth::user()->role === 'cliente' && $request->has('mis_productos')) {
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
                // Necesitarías una relación o campo para "populares", por ejemplo, contar ventas
                // $query->withCount('ordenItems')->orderBy('orden_items_count', 'desc'); // Asumiendo relación ordenItems
                $query->orderBy('created_at', 'desc'); // Placeholder
                break;
            default: // recientes
                $query->orderBy('created_at', 'desc');
        }

        $productos = $query->paginate(12)->appends($request->query());
        $categoriasFiltro = Categoria::orderBy('nombre')->get(); // Para el dropdown de filtro

        return view('productos.index', compact('productos', 'categoriasFiltro'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Producto::class);

        try {
            Log::info('ProductoController@create: Iniciando método por User ID: ' . (Auth::id() ?? 'Guest'));
            $categorias = Categoria::orderBy('nombre')->get();
            Log::info('ProductoController@create: Categorías cargadas: ' . $categorias->count());
            Log::info('ProductoController@create: Renderizando vista productos.create');
            return view('productos.create', compact('categorias'));
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::warning('ProductoController@create: Intento de acceso no autorizado por User ID: ' . (Auth::id() ?? 'Guest'));
            return redirect()->route('productos.index')->with('error', 'No tienes permiso para crear productos.');
        } catch (\Exception $e) {
            Log::error('ProductoController@create: Error: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return redirect()->route('productos.index')->with('error', 'Error al cargar el formulario de creación: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Producto::class);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
            'imagenes.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Max 2MB por imagen
            'imagenes' => 'max:5', // Limitar a 5 imágenes
            'categorias' => 'nullable|array',
            'categorias.*' => 'exists:categorias,id'
        ]);

        $producto = new Producto();
        $producto->nombre = $validated['nombre'];
        $producto->descripcion = $validated['descripcion'];
        $producto->precio = $validated['precio'];
        $producto->stock = $validated['stock'];
        $producto->usuario_id = Auth::id();
        $producto->save();

        if (!empty($validated['categorias'])) {
            $producto->categorias()->attach($validated['categorias']);
        }

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $imagenFile) {
                $nombreArchivo = Str::slug($producto->nombre) . '_' . time() . '_' . Str::random(5) . '.' . $imagenFile->getClientOriginalExtension();
                $ruta = $imagenFile->storeAs('productos', $nombreArchivo, 'public');
                ProductoImagen::create([
                    'producto_id' => $producto->id,
                    'ruta_imagen' => $ruta
                ]);
            }
        }

        return redirect()->route('productos.show', $producto)->with('success', 'Producto creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto) // Route Model Binding
    {
        $this->authorize('view', $producto);
        $producto->load('imagenes', 'usuario', 'categorias'); // Cargar relaciones

        return view('productos.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        $this->authorize('update', $producto);
        $producto->load('imagenes', 'categorias');

        $categorias = Categoria::orderBy('nombre')->get();
        $categoriasSeleccionadas = $producto->categorias->pluck('id')->toArray();

        return view('productos.edit', compact('producto', 'categorias', 'categoriasSeleccionadas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        $this->authorize('update', $producto);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
            'imagenes.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'imagenes' => 'max:5',
            'categorias' => 'nullable|array',
            'categorias.*' => 'exists:categorias,id',
            'eliminar_imagenes' => 'nullable|array',
            'eliminar_imagenes.*' => 'exists:producto_imagenes,id'
        ]);

        $producto->nombre = $validated['nombre'];
        $producto->descripcion = $validated['descripcion'];
        $producto->precio = $validated['precio'];
        $producto->stock = $validated['stock'];
        $producto->save();

        if (isset($validated['categorias'])) {
            $producto->categorias()->sync($validated['categorias']);
        } else {
            $producto->categorias()->detach(); // Si no se envían categorías, se eliminan todas las asociaciones
        }

        // Eliminar imágenes seleccionadas
        if (!empty($validated['eliminar_imagenes'])) {
            foreach ($validated['eliminar_imagenes'] as $imagenId) {
                $imagen = ProductoImagen::where('id', $imagenId)->where('producto_id', $producto->id)->first();
                if ($imagen) {
                    Storage::disk('public')->delete($imagen->ruta_imagen);
                    $imagen->delete();
                }
            }
        }

        // Agregar nuevas imágenes
        if ($request->hasFile('imagenes')) {
            // Contar imágenes existentes para no exceder el límite
            $imagenesExistentesCount = $producto->imagenes()->count();
            $imagenesNuevasCount = count($request->file('imagenes'));

            if (($imagenesExistentesCount + $imagenesNuevasCount) > 5) {
                return back()->withErrors(['imagenes' => 'No puedes subir más de 5 imágenes en total.'])->withInput();
            }

            foreach ($request->file('imagenes') as $imagenFile) {
                $nombreArchivo = Str::slug($producto->nombre) . '_' . time() . '_' . Str::random(5) . '.' . $imagenFile->getClientOriginalExtension();
                $ruta = $imagenFile->storeAs('productos', $nombreArchivo, 'public');
                ProductoImagen::create([
                    'producto_id' => $producto->id,
                    'ruta_imagen' => $ruta
                ]);
            }
        }

        return redirect()->route('productos.show', $producto)->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        $this->authorize('delete', $producto);

        // Eliminar imágenes físicas y registros de la BD
        foreach ($producto->imagenes as $imagen) {
            Storage::disk('public')->delete($imagen->ruta_imagen);
            $imagen->delete(); // Esto también podría hacerse con onDelete('cascade') en la migración
        }

        $producto->categorias()->detach(); // Desasociar categorías
        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
    }
}
