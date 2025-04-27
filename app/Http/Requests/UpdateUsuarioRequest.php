<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUsuarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $usuario = $this->route('usuario');
        $currentUser = Auth::user();

        if ($currentUser->role === 'administrador') {
            return true;
        }

        if ($currentUser->role === 'gerente') {
            return $usuario->role === 'cliente';
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'sometimes|required|string|max:255',
            'correo' => 'sometimes|required|email|unique:usuarios,correo,' . $this->usuario->id,
            'clave' => 'sometimes|nullable|string|min:6',
            'role' => 'sometimes|required|in:cliente,administrador,gerente',
            'es_comprador' => 'sometimes|boolean',
            'es_vendedor' => 'sometimes|boolean',
        ];
    }
}
