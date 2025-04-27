<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreOrdenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && (Auth::user()->es_comprador || in_array(Auth::user()->role, ['administrador', 'gerente']));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'usuario_id' => 'sometimes|exists:usuarios,id',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'estado' => 'sometimes|in:pendiente,pagado,enviado,entregado,cancelado',
        ];
    }

    protected function prepareForValidation()
    {
        if (!$this->has('usuario_id')) {
            $this->merge([
                'usuario_id' => Auth::user()->id,
            ]);
        }

        if (!$this->has('estado')) {
            $this->merge([
                'estado' => 'pendiente',
            ]);
        }
    }
}
