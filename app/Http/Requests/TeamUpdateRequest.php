<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TeamUpdateRequest extends FormRequest
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
            'nombre' => 'sometimes|required|string|max:255',
            'logo' => 'nullable|string|url',
            'ciudad' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'formato_preferido' => 'nullable|string',
            'estado' => 'nullable|string|in:activo,inactivo'
        ];
    }
}
