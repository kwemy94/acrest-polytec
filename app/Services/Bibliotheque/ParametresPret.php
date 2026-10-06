<?php

namespace App\Services\Bibliotheque;

use App\Enums\TypeAdherent;
use App\Models\Parametre;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

/**
 * Paramètres de prêt : valeurs enregistrées par l'administrateur (table `parametres`),
 * à défaut celles de config/acrest.php.
 */
class ParametresPret
{
    private const CLE = 'parametres_pret';

    private const CACHE = 'bibliotheque.parametres_pret';

    public function tous(): array
    {
        return Cache::rememberForever(self::CACHE, function () {
            $enregistres = json_decode((string) Parametre::find(self::CLE)?->valeur, true) ?: [];

            return array_replace_recursive(config('acrest.bibliotheque.pret'), $enregistres);
        });
    }

    public function enregistrer(array $valeurs): void
    {
        Parametre::updateOrCreate(['cle' => self::CLE], ['valeur' => json_encode($valeurs)]);
        Cache::forget(self::CACHE);
    }

    private function entier(string $cle): int
    {
        return (int) Arr::get($this->tous(), $cle);
    }

    public function dureePret(TypeAdherent $type): int
    {
        return $this->entier("types.{$type->value}.duree");
    }

    public function maxPrets(TypeAdherent $type): int
    {
        return $this->entier("types.{$type->value}.max");
    }

    public function maxProlongations(): int
    {
        return $this->entier('max_prolongations');
    }

    public function delaiRetrait(): int
    {
        return $this->entier('delai_retrait');
    }

    public function rappelAvantEcheance(): int
    {
        return $this->entier('rappel_avant_echeance');
    }

    public function relanceTousLes(): int
    {
        return $this->entier('relance_tous_les');
    }
}
