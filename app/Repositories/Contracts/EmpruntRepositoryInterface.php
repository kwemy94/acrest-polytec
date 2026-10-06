<?php

namespace App\Repositories\Contracts;

use App\Models\Adherent;
use App\Models\Document;
use App\Models\Emprunt;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface EmpruntRepositoryInterface extends RepositoryInterface
{
    /** @param array{q?:string, statut?:string, retard?:bool, adherent?:int} $filtres */
    public function rechercher(array $filtres, int $perPage = 20): LengthAwarePaginator;

    public function deAdherent(Adherent $adherent): Collection;

    public function compterActifs(Adherent $adherent): int;

    public function aDesRetards(Adherent $adherent): bool;

    public function actifPourDocument(Adherent $adherent, Document $document): ?Emprunt;

    /** Plus ancienne demande en attente pour ce document (file d'attente). */
    public function prochaineDemande(Document $document): ?Emprunt;

    public function compterDemandes(Document $document): int;

    /** @return array{demandes:int, reserves:int, en_cours:int, retards:int} */
    public function compteurs(): array;

    public function reservationsExpirees(): Collection;

    public function aRappeler(int $joursAvant): Collection;

    public function aRelancer(int $intervalle): Collection;
}
