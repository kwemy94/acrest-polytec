<?php

namespace App\Models;

use App\Enums\NiveauAcces;
use App\Enums\TypeRessource;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Version électronique d'un document (PDF, EPUB, audio…), stockée sur le disque privé. */
class RessourceNumerique extends Model
{
    protected $table = 'ressources_numeriques';

    protected $fillable = [
        'document_id',
        'titre',
        'fichier',
        'nom_original',
        'mime',
        'type',
        'taille',
        'version',
        'niveau_acces',
        'ajoute_par',
    ];

    protected $hidden = ['fichier'];

    protected function casts(): array
    {
        return [
            'type' => TypeRessource::class,
            'niveau_acces' => NiveauAcces::class,
            'taille' => 'integer',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function auteurAjout(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ajoute_par');
    }

    protected function libelle(): Attribute
    {
        return Attribute::get(fn () => $this->titre ?: $this->type->libelle().' — version '.$this->version);
    }

    protected function tailleLisible(): Attribute
    {
        return Attribute::get(function () {
            $o = $this->taille;

            return match (true) {
                $o >= 1048576 => number_format($o / 1048576, 1, ',', ' ').' Mo',
                $o >= 1024 => number_format($o / 1024, 0, ',', ' ').' Ko',
                default => $o.' o',
            };
        });
    }
}
