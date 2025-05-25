<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\ProductoImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Utiliza eager loading para cargar las imágenes junto con los productos
        $productos = Producto::with('imagenes')->paginate(12);

        return view('productos.index', compact('productos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación...

        $usuario = Auth::user();

        if ($usuario->role !== 'cliente') {
            return back()->with('error', 'Solo los clientes pueden vender productos');
        }

        // Crear producto...
        $producto = Producto::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'stock' => $request->stock,
            'usuario_id' => Auth::id(),
        ]);

        // Subir múltiples imágenes
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $imagen) {
                $ruta = $imagen->store('productos', 'public');

                ProductoImagen::create([
                    'producto_id' => $producto->id,
                    'ruta_imagen' => $ruta
                ]);
            }
        }

        // Redireccionar...
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Verificar si el usuario es cliente (para mostrar o no el botón de compra)
        $puedeComprar = false;
        if (Auth::check()) {
            $puedeComprar = Auth::user()->role === 'cliente';
        }

        $producto = Producto::with(['imagenes', 'categorias', 'usuario'])
            ->findOrFail($id);

        return view('productos.show', compact('producto', 'puedeComprar'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        //
    }
}
