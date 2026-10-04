<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TraitementPaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', 'in:valider,rejeter'],
            'note' => ['nullable', 'string', 'max:500', 'required_if:decision,rejeter'],
        ];
    }

    public function messages(): array
    {
        return ['note.required_if' => 'Indiquez le motif du rejet : il sera visible par le candidat.'];
    }
}
