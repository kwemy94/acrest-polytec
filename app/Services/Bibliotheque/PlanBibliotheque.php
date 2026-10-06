<?php

namespace App\Services\Bibliotheque;

use App\Models\Localisation;
use Illuminate\Support\Collection;

/**
 * Plan des espaces de la bibliothèque (arborescence des localisations).
 * La table est petite : elle est chargée une fois par requête pour calculer chemins et sous-arbres.
 */
class PlanBibliotheque
{
    private ?Collection $localisations = null;

    /** @return Collection<int, Localisation> indexée par id */
    public function toutes(): Collection
    {
        return $this->localisations ??= Localisation::orderBy('nom')->get()->keyBy('id');
    }

    public function oublier(): void
    {
        $this->localisations = null;
    }

    /** « Salle Sciences → Rayon Informatique → Étagère A ». */
    public function chemin(?int $id, string $separateur = ' → '): string
    {
        $noms = [];
        $vus = [];

        while ($id && ($loc = $this->toutes()->get($id)) && ! isset($vus[$id])) {
            $vus[$id] = true;
            array_unshift($noms, $loc->nom);
            $id = $loc->parent_id;
        }

        return implode($separateur, $noms);
    }

    /** Identifiants de la localisation et de tous ses sous-espaces. */
    public function sousArbre(int $id): array
    {
        $ids = [$id];
        $enfants = $this->toutes()->groupBy('parent_id');

        for ($i = 0; $i < count($ids); $i++) {
            foreach ($enfants->get($ids[$i], []) as $enfant) {
                $ids[] = $enfant->id;
            }
        }

        return $ids;
    }

    /**
     * Arbre aplati, chaque élément avec sa profondeur : pour les listes et les menus déroulants.
     *
     * @return list<array{localisation: Localisation, profondeur: int}>
     */
    public function arbre(?int $exclure = null): array
    {
        $enfants = $this->toutes()->groupBy(fn (Localisation $l) => $l->parent_id ?? 0);
        $exclus = $exclure ? $this->sousArbre($exclure) : [];
        $resultat = [];

        $parcourir = function (int $parent, int $profondeur) use (&$parcourir, &$resultat, $enfants, $exclus) {
            foreach ($enfants->get($parent, []) as $loc) {
                if (in_array($loc->id, $exclus, true)) {
                    continue;
                }
                $resultat[] = ['localisation' => $loc, 'profondeur' => $profondeur];
                $parcourir($loc->id, $profondeur + 1);
            }
        };
        $parcourir(0, 0);

        return $resultat;
    }

    /** @return array<int, string> id => libellé indenté */
    public function options(?int $exclure = null): array
    {
        return collect($this->arbre($exclure))
            ->mapWithKeys(fn ($l) => [$l['localisation']->id => str_repeat('— ', $l['profondeur']).$l['localisation']->nom])
            ->all();
    }
}
