<?php

namespace App\Http\Requests\Admin\Bibliotheque;

use App\Enums\EtatPhysique;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'isbn' => $this->isbn ? strtoupper(preg_replace('/[^0-9Xx]/', '', (string) $this->isbn)) : null,
            'consultation_sur_place' => $this->boolean('consultation_sur_place'),
            // Mots-clés : séparés par des virgules, normalisés.
            'mots_cles' => collect(explode(',', (string) $this->mots_cles))->map(fn ($m) => trim($m))->filter()->unique()->implode(', ') ?: null,
        ]);
    }

    public function rules(): array
    {
        $creation = $this->isMethod('post');

        return [
            'titre' => ['required', 'string', 'max:255'],
            'sous_titre' => ['nullable', 'string', 'max:255'],
            'auteurs' => ['nullable', 'string', 'max:2000'],
            'type_document_id' => ['required', 'integer', Rule::exists('types_documents', 'id')->where('actif', true)],
            'categorie_id' => ['nullable', 'integer', 'exists:categories,id'],
            'editeur' => ['nullable', 'string', 'max:255'],
            'annee_publication' => ['nullable', 'integer', 'min:1500', 'max:'.(now()->year + 1)],
            'isbn' => ['nullable', 'string', 'between:8,13'],
            'langue' => ['required', Rule::in(array_keys(config('acrest.langues')))],
            'description' => ['nullable', 'string', 'max:5000'],
            'mots_cles' => ['nullable', 'string', 'max:500'],
            'nombre_pages' => ['nullable', 'integer', 'min:1', 'max:20000'],
            'cote' => ['nullable', 'string', 'max:30'],
            'consultation_sur_place' => ['boolean'],

            // Premiers exemplaires (création uniquement)
            'exemplaires' => [$creation ? 'required' : 'exclude', 'integer', 'min:0', 'max:50'],
            'localisation_id' => [$creation ? 'nullable' : 'exclude', 'integer', 'exists:localisations,id'],
            'date_acquisition' => [$creation ? 'nullable' : 'exclude', 'date', 'before_or_equal:today'],
            'source_acquisition' => [$creation ? 'nullable' : 'exclude', 'string', 'max:100'],
            'etat_physique' => [$creation ? 'nullable' : 'exclude', Rule::enum(EtatPhysique::class)],
        ];
    }

    public function attributes(): array
    {
        return [
            'sous_titre' => 'sous-titre',
            'type_document_id' => 'type de document',
            'categorie_id' => 'catégorie',
            'annee_publication' => 'année de publication',
            'isbn' => 'ISBN/ISSN',
            'mots_cles' => 'mots-clés',
            'nombre_pages' => 'nombre de pages',
            'exemplaires' => 'nombre d\'exemplaires',
            'localisation_id' => 'localisation',
            'date_acquisition' => 'date d\'acquisition',
        ];
    }

    /** Champs de la notice. */
    public function notice(): array
    {
        return collect($this->validated())->only([
            'titre', 'sous_titre', 'type_document_id', 'categorie_id', 'editeur', 'annee_publication', 'isbn',
            'langue', 'description', 'mots_cles', 'nombre_pages', 'cote', 'consultation_sur_place',
        ])->all();
    }

    /** Auteurs saisis un par ligne (ou séparés par « ; »). @return list<string> */
    public function auteurs(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/[\r\n;]+/', (string) $this->validated('auteurs')))));
    }

    /** Attributs des premiers exemplaires. */
    public function exemplaire(): array
    {
        return array_filter([
            'localisation_id' => $this->validated('localisation_id'),
            'date_acquisition' => $this->validated('date_acquisition'),
            'source_acquisition' => $this->validated('source_acquisition'),
            'etat_physique' => $this->validated('etat_physique') ?: EtatPhysique::Neuf->value,
        ]);
    }
}
