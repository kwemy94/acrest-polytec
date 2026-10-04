<?php

namespace App\Http\Requests\Inscription;

use App\Enums\Sexe;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class IdentiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nom' => Str::upper(trim((string) $this->nom)),
            'prenom' => $this->prenom ? Str::title(trim($this->prenom)) : null,
            'lieu_naissance' => Str::title(trim((string) $this->lieu_naissance)),
            'cni' => Str::upper(preg_replace('/\s+/', '', (string) $this->cni)),
        ]);
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:100'],
            'prenom' => ['nullable', 'string', 'max:100'],
            'sexe' => ['required', Rule::enum(Sexe::class)],
            'date_naissance' => ['required', 'date', 'after:1940-01-01', 'before:'.now()->subYears(14)->toDateString()],
            'lieu_naissance' => ['required', 'string', 'max:100'],
            'pays' => ['required', 'string', Rule::in(collect(config('acrest.pays'))->flatten()->all())],
            'cni' => [
                'required', 'string', 'min:5', 'max:30', 'regex:/^[A-Z0-9\-\/]+$/',
                Rule::unique('inscriptions', 'cni')->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'date_naissance.before' => 'Vous devez avoir au moins 14 ans pour vous inscrire.',
            'cni.unique' => 'Un dossier existe déjà avec ce numéro de pièce d\'identité. Utilisez « Retrouver mon code » pour le consulter.',
            'cni.regex' => 'Le numéro ne doit contenir que des lettres, des chiffres, « - » ou « / ».',
        ];
    }

    public function attributes(): array
    {
        return [
            'date_naissance' => 'date de naissance',
            'lieu_naissance' => 'lieu de naissance',
            'cni' => 'numéro de CNI / passeport',
        ];
    }
}
