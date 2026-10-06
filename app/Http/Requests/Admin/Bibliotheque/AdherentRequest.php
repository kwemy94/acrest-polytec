<?php

namespace App\Http\Requests\Admin\Bibliotheque;

use App\Enums\StatutAdherent;
use App\Enums\TypeAdherent;
use App\Models\Inscription;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Création / modification d'un adhérent.
 * À la création d'un étudiant, on le choisit parmi les étudiants en règle : son identité,
 * ses coordonnées et son matricule (code d'inscription) sont repris de son dossier.
 */
class AdherentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    private function creation(): bool
    {
        return $this->isMethod('post');
    }

    private function etudiantDepuisDossier(): bool
    {
        return $this->creation() && $this->input('type') === TypeAdherent::Etudiant->value;
    }

    protected function prepareForValidation(): void
    {
        // Étudiant : les informations viennent du dossier d'inscription, pas de la saisie.
        if ($this->etudiantDepuisDossier()) {
            $inscription = $this->inscription_id ? Inscription::find($this->inscription_id) : null;
            $this->merge([
                'matricule' => $inscription?->code,
                'nom' => $inscription?->nom,
                'prenom' => $inscription?->prenom,
                'email' => $inscription?->email,
                'telephone' => $inscription?->telephone,
            ]);
        }

        $this->merge([
            'matricule' => Str::upper(trim((string) $this->matricule)),
            'nom' => Str::upper(trim((string) $this->nom)),
            'email' => $this->email ? Str::lower(trim((string) $this->email)) : null,
            'telephone' => $this->telephone ? preg_replace('/[^0-9+]/', '', (string) $this->telephone) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'inscription_id' => $this->etudiantDepuisDossier()
                ? ['required', 'integer',
                    // Uniquement un étudiant en règle (dossier validé, frais payés) sans fiche adhérent
                    Rule::in(Inscription::enRegle()->sansFicheAdherent()->pluck('id')->all())]
                : ['exclude'],
            'matricule' => ['required', 'string', 'max:30', Rule::unique('adherents', 'matricule')->ignore($this->route('adherent'))],
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['nullable', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'type' => ['required', Rule::enum(TypeAdherent::class)],
            'date_inscription' => ['required', 'date'],
            'date_expiration' => ['nullable', 'date', 'after_or_equal:date_inscription'],
            'statut' => ['required', Rule::enum(StatutAdherent::class)],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'inscription_id.required' => 'Choisissez l\'étudiant dans la liste des étudiants en règle.',
            'inscription_id.in' => 'Cet étudiant n\'est pas en règle (dossier validé et frais payés) ou possède déjà une fiche adhérent.',
        ];
    }

    public function attributes(): array
    {
        return [
            'inscription_id' => 'étudiant',
            'date_inscription' => 'date d\'inscription',
            'date_expiration' => 'date d\'expiration',
        ];
    }
}
