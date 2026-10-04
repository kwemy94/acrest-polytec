<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class RechercheDossierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => Str::upper(trim((string) $this->code)),
            'email' => Str::lower(trim((string) $this->email)),
        ]);
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email'],
        ];
    }

    public function attributes(): array
    {
        return ['code' => 'code d\'inscription'];
    }
}
