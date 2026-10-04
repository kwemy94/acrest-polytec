<?php

namespace App\Http\Requests\Inscription;

use App\Repositories\Contracts\SpecialiteRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

class FormationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $existe = Rule::exists('specialites', 'id')->where('active', true);

        return [
            'choix' => ['required', 'array'],
            'choix.1.filiere' => ['required', 'integer'],
            'choix.1.specialite' => ['required', 'integer', $existe],
            'choix.2.filiere' => ['nullable', 'integer'],
            'choix.2.specialite' => ['nullable', 'required_with:choix.2.filiere', 'integer', $existe, 'different:choix.1.specialite'],
            'choix.3.filiere' => ['nullable', 'integer'],
            'choix.3.specialite' => ['nullable', 'required_with:choix.3.filiere', 'integer', $existe, 'different:choix.1.specialite', 'different:choix.2.specialite'],
        ];
    }

    public function messages(): array
    {
        return [
            'choix.1.filiere.required' => 'Choisissez la filière de votre premier choix.',
            'choix.1.specialite.required' => 'Choisissez la spécialité de votre premier choix.',
            'choix.*.specialite.required_with' => 'Choisissez une spécialité pour cette filière, ou retirez ce choix.',
            'choix.*.specialite.different' => 'Chaque choix doit porter sur une spécialité différente.',
            'choix.*.specialite.exists' => 'Cette spécialité n\'est pas disponible.',
        ];
    }

    /** Vérifie que chaque spécialité appartient bien à la filière choisie. */
    public function after(): array
    {
        return [function (Validator $validator) {
            $specialites = app(SpecialiteRepositoryInterface::class);
            foreach ((array) $this->input('choix', []) as $rang => $choix) {
                $filiere = (int) ($choix['filiere'] ?? 0);
                $specialite = (int) ($choix['specialite'] ?? 0);
                if ($filiere && $specialite && ! $specialites->appartientAFiliere($specialite, $filiere)) {
                    $validator->errors()->add("choix.$rang.specialite", 'Cette spécialité ne fait pas partie de la filière choisie.');
                }
            }
        }];
    }

    /** Ne garde que les choix complets, renumérotés 1, 2, 3. */
    public function choix(): array
    {
        return collect($this->validated('choix'))
            ->filter(fn ($c) => ! empty($c['specialite']))
            ->sortKeys()
            ->values()
            ->mapWithKeys(fn ($c, $i) => [$i + 1 => ['filiere' => (int) $c['filiere'], 'specialite' => (int) $c['specialite']]])
            ->all();
    }
}
