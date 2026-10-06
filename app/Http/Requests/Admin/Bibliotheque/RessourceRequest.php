<?php

namespace App\Http\Requests\Admin\Bibliotheque;

use App\Enums\NiveauAcces;
use App\Enums\TypeRessource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Ajout (POST, avec fichier) ou modification des droits (PATCH) d'une ressource numérique. */
class RessourceRequest extends FormRequest
{
    /** Types détectés à partir du contenu du fichier (docx et epub sont parfois vus comme des archives zip). */
    private const TYPES_MIME = [
        'application/pdf', 'application/epub+zip', 'application/zip',
        'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.oasis.opendocument.text', 'application/rtf', 'text/rtf',
        'audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/x-wav', 'audio/ogg', 'audio/mp4', 'audio/x-m4a',
        'video/mp4', 'video/webm',
    ];

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $extensions = implode(',', TypeRessource::EXTENSIONS);

        return [
            'fichier' => [
                $this->isMethod('post') ? 'required' : 'exclude',
                'file',
                // L'extension ET le contenu réel du fichier doivent correspondre à un format accepté.
                "extensions:{$extensions}",
                'mimetypes:'.implode(',', self::TYPES_MIME),
                'max:'.(config('acrest.bibliotheque.fichier_max_mo') * 1024),
            ],
            'titre' => ['nullable', 'string', 'max:255'],
            'version' => ['nullable', 'string', 'max:20'],
            'niveau_acces' => ['required', Rule::enum(NiveauAcces::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'fichier.max' => 'Le fichier ne doit pas dépasser '.config('acrest.bibliotheque.fichier_max_mo').' Mo.',
            'fichier.extensions' => 'Formats acceptés : '.implode(', ', TypeRessource::EXTENSIONS).'.',
            'fichier.mimetypes' => 'Le contenu du fichier ne correspond pas à un format accepté.',
        ];
    }

    public function attributes(): array
    {
        return ['niveau_acces' => 'niveau d\'accès'];
    }
}
