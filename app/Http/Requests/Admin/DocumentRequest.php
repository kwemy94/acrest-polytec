<?php

namespace App\Http\Requests\Admin;

use App\Enums\TypeDocument;
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
            'isbn' => $this->isbn ? preg_replace('/[^0-9Xx]/', '', (string) $this->isbn) : null,
            'consultation_sur_place' => $this->boolean('consultation_sur_place'),
            'telechargeable' => $this->boolean('telechargeable'),
            'supprimer_fichier' => $this->boolean('supprimer_fichier'),
        ]);
    }

    public function rules(): array
    {
        return [
            'titre' => ['required', 'string', 'max:255'],
            'auteurs' => ['required', 'string', 'max:255'],
            'editeur' => ['nullable', 'string', 'max:255'],
            'annee_publication' => ['nullable', 'integer', 'min:1800', 'max:'.(now()->year + 1)],
            'isbn' => ['nullable', 'string', 'max:20'],
            'cote' => ['nullable', 'string', 'max:30'],
            'type' => ['required', Rule::enum(TypeDocument::class)],
            'langue' => ['required', Rule::in(array_keys(config('acrest.langues')))],
            'filiere_id' => ['nullable', 'integer', 'exists:filieres,id'],
            'resume' => ['nullable', 'string', 'max:3000'],
            'consultation_sur_place' => ['boolean'],
            // Version numérique
            'fichier' => ['nullable', 'file', 'mimes:pdf', 'extensions:pdf', 'max:'.(config('acrest.bibliotheque.pdf_max_mo') * 1024)],
            'telechargeable' => ['boolean'],
            'supprimer_fichier' => ['boolean'],
            // Nombre d'exemplaires créés avec la notice (création uniquement).
            'exemplaires' => [$this->isMethod('post') ? 'required' : 'exclude', 'integer', 'min:0', 'max:50'],
        ];
    }

    public function attributes(): array
    {
        return [
            'annee_publication' => 'année de publication',
            'filiere_id' => 'filière',
            'resume' => 'résumé',
            'exemplaires' => 'nombre d\'exemplaires',
            'fichier' => 'fichier PDF',
        ];
    }

    public function messages(): array
    {
        return ['fichier.max' => 'Le fichier PDF ne doit pas dépasser '.config('acrest.bibliotheque.pdf_max_mo').' Mo.'];
    }

    /** Attributs de la notice, sans les champs liés aux fichiers et aux exemplaires. */
    public function notice(): array
    {
        return collect($this->validated())->except(['exemplaires', 'fichier', 'supprimer_fichier'])->all();
    }
}
