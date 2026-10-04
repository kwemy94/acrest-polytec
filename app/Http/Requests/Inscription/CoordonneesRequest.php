<?php

namespace App\Http\Requests\Inscription;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class CoordonneesRequest extends FormRequest
{
    public const TELEPHONE = '/^\+?[0-9]{8,15}$/';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $tel = fn ($v) => preg_replace('/[\s.\-()]/', '', (string) $v);

        $this->merge([
            'telephone' => $tel($this->telephone),
            'contact_parent' => $tel($this->contact_parent),
            'email' => Str::lower(trim((string) $this->email)),
            'nom_pere' => $this->nom_pere ? Str::title(trim($this->nom_pere)) : null,
            'nom_mere' => Str::title(trim((string) $this->nom_mere)),
        ]);
    }

    public function rules(): array
    {
        return [
            'telephone' => ['required', 'regex:'.self::TELEPHONE],
            'email' => ['required', 'email:rfc', 'max:150'],
            'nom_pere' => ['nullable', 'string', 'max:150'],
            'nom_mere' => ['required', 'string', 'max:150'],
            'contact_parent' => ['required', 'regex:'.self::TELEPHONE, 'different:telephone'],
        ];
    }

    public function messages(): array
    {
        return [
            'telephone.regex' => 'Saisissez un numéro valide, par exemple 6 77 00 00 00 ou +237 677 00 00 00.',
            'contact_parent.regex' => 'Saisissez un numéro valide pour le parent ou tuteur.',
            'contact_parent.different' => 'Le numéro du parent doit être différent du vôtre.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nom_pere' => 'nom du père',
            'nom_mere' => 'nom de la mère',
            'contact_parent' => 'téléphone du parent ou tuteur',
        ];
    }
}
