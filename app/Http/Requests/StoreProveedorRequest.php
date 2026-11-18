<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProveedorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ruc' => ['required', 'digits:11', 'unique:proveedores,ruc'],
            'razon_social' => ['required', 'max:100'],
            'telefono' => ['required', 'digits_between:7,9'],
            'email' => ['required', 'email', 'max:100', 'unique:proveedores,email'],
            'direccion' => ['required', 'max:200'],
        ];
    }

    public function messages(): array
    {
        return [
            'ruc.required' => 'El RUC es obligatorio.',
            'ruc.digits' => 'El RUC debe tener exactamente 11 dígitos.',
            'ruc.unique' => 'Ya existe un proveedor con ese RUC.',
            'razon_social.required' => 'La razón social es obligatoria.',
            'razon_social.max' => 'La razón social admite como máximo 100 caracteres.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.digits_between' => 'El teléfono debe tener entre 7 y 9 dígitos.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo no tiene un formato válido.',
            'email.max' => 'El correo admite como máximo 100 caracteres.',
            'email.unique' => 'Ya existe un proveedor con ese correo.',
            'direccion.required' => 'La dirección es obligatoria.',
            'direccion.max' => 'La dirección admite como máximo 200 caracteres.',
        ];
    }
}
