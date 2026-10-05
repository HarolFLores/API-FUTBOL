<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ChallengeStoreRequest extends FormRequest
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
            'challenger_team_id' => 'required|exists:teams,id',
            'challenged_team_id' => 'nullable|exists:teams,id',
            'tipo_reto' => 'required|in:directo,abierto',
            'fecha_propuesta' => 'required|date|after_or_equal:today',
            'hora_propuesta' => 'required|date_format:H:i',
            'ubicacion' => 'nullable|string|max:255',
            'formato' => 'nullable|string|max:255',
            'nivel_sugerido' => 'nullable|string|max:255',
            'costo_compartido' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string'
        ];
    }
}
