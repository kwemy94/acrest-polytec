<?php

namespace App\Http\Requests;

use App\Enums\OperateurPaiement;
use App\Http\Requests\Inscription\CoordonneesRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => Str::upper(trim((string) $this->code)),
            'telephone' => preg_replace('/[\s.\-()]/', '', (string) $this->telephone),
            'reference' => Str::upper(preg_replace('/\s+/', '', (string) $this->reference)),
        ]);
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:20'],
            'operateur' => ['required', Rule::enum(OperateurPaiement::class)],
            'telephone' => ['required', 'regex:'.CoordonneesRequest::TELEPHONE],
            'reference' => ['required', 'string', 'min:6', 'max:50', 'regex:/^[A-Z0-9.\-]+$/'],
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'code d\'inscription',
            'operateur' => 'opérateur',
            'telephone' => 'numéro ayant payé',
            'reference' => 'référence de transaction',
        ];
    }
}
