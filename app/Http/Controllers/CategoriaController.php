<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categorias = Categoria::orderBy('nombre')->paginate(10);
        return view('categorias.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categorias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre',
            'descripcion' => 'nullable|string',
        ]);

        Categoria::create($validated);

        return redirect()->route('categorias.index')->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Display the specified resource.
     * (Optional: Implement if you need a dedicated show page for a category)
     */
    public function show(Categoria $categoria)
    {
        // Example: Load products for this category if needed
        // $categoria->load('productos');
        // return view('categorias.show', compact('categoria'));
        return redirect()->route('categorias.index'); // Or implement a show view
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categoria $categoria)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre,' . $categoria->id,
            'descripcion' => 'nullable|string',
        ]);

        $categoria->update($validated);

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categoria $categoria)
    {
        // Consider implications: what happens to products in this category?
        // The pivot table 'categoria_producto' has onDelete('cascade'),
        // so entries there will be removed. Products themselves won't be deleted.
        if ($categoria->productos()->count() > 0) {
            return redirect()->route('categorias.index')->with('error', 'No se puede eliminar la categoría porque tiene productos asociados. Por favor, reasigne o elimine los productos primero.');
        }

        $categoria->delete();

        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada exitosamente.');
    }
}
