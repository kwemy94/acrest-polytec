<?php

namespace App\Http\Requests\Inscription;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DiplomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'diplome' => ['required', Rule::in(config('acrest.diplomes'))],
            'option_diplome' => ['nullable', 'string', 'max:50'],
            'annee_obtention' => ['nullable', 'integer', 'between:1980,'.now()->year],
        ];
    }

    public function attributes(): array
    {
        return [
            'option_diplome' => 'série ou option',
            'annee_obtention' => 'année d\'obtention',
        ];
    }
}
