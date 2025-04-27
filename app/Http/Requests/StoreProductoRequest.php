<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreProductoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && (Auth::user()->es_vendedor || in_array(Auth::user()->role, ['administrador', 'gerente']));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'usuario_id' => 'sometimes|exists:usuarios,id',
            'categorias' => 'sometimes|array',
            'categorias.*' => 'exists:categorias,id',
        ];
    }

    protected function prepareForValidation()
    {
        if (!$this->has('usuario_id')) {
            $this->merge([
                'usuario_id' => Auth::id(),
            ]);
        }
    }
}
