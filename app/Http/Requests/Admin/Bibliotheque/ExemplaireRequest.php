<?php

namespace App\Http\Requests\Admin\Bibliotheque;

use App\Enums\EtatPhysique;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Ajout (POST, avec un nombre) ou modification (PUT) d'un exemplaire. */
class ExemplaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['code_barres' => $this->code_barres ? strtoupper(trim((string) $this->code_barres)) : null]);
    }

    public function rules(): array
    {
        $creation = $this->isMethod('post');

        return [
            'nombre' => [$creation ? 'required' : 'exclude', 'integer', 'min:1', 'max:50'],
            'localisation_id' => [$creation ? 'nullable' : 'exclude', 'integer', 'exists:localisations,id'],
            'code_barres' => ['nullable', 'string', 'max:50', Rule::unique('exemplaires', 'code_barres')->ignore($this->route('exemplaire'))],
            'date_acquisition' => ['nullable', 'date', 'before_or_equal:today'],
            'source_acquisition' => ['nullable', 'string', 'max:100'],
            'etat_physique' => ['required', Rule::enum(EtatPhysique::class)],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'code_barres' => 'code-barres',
            'date_acquisition' => 'date d\'acquisition',
            'source_acquisition' => 'source d\'acquisition',
            'etat_physique' => 'état physique',
            'localisation_id' => 'localisation',
        ];
    }
}
