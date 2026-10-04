<?php

namespace App\Http\Requests\Inscription;

use Illuminate\Foundation\Http\FormRequest;

class FinalisationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['certifie' => ['accepted']];
    }

    public function messages(): array
    {
        return ['certifie.accepted' => 'Cochez cette case pour certifier l\'exactitude de vos informations.'];
    }
}
