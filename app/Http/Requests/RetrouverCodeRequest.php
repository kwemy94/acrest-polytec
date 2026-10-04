<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class RetrouverCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => Str::lower(trim((string) $this->email)),
            'cni' => Str::upper(preg_replace('/\s+/', '', (string) $this->cni)),
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'cni' => ['required', 'string', 'max:30'],
        ];
    }

    public function attributes(): array
    {
        return ['cni' => 'numéro de CNI / passeport'];
    }
}
