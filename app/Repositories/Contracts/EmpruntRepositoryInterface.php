<?php

namespace App\Repositories\Contracts;

use App\Models\Document;
use App\Models\Emprunt;
use App\Models\Inscription;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface EmpruntRepositoryInterface extends RepositoryInterface
{
    /** @param array{q?:string, statut?:string, retard?:bool} $filtres */
    public function rechercher(array $filtres, int $perPage = 20): LengthAwarePaginator;

    public function duLecteur(Inscription $inscription): Collection;

    public function compterActifs(Inscription $inscription): int;

    public function aDesRetards(Inscription $inscription): bool;

    public function demandeActive(Inscription $inscription, Document $document): bool;

    /** Plus ancienne demande en attente pour ce document (file d'attente). */
    public function prochaineDemande(Document $document): ?Emprunt;

    public function compterDemandes(Document $document): int;

    /** @return array{demandes:int, reserves:int, en_cours:int, retards:int} */
    public function compteurs(): array;

    /** Réservations dont la date limite de retrait est dépassée. */
    public function reservationsExpirees(): Collection;

    /** Prêts dont l'échéance tombe dans N jours et pas encore rappelés. */
    public function aRappeler(int $joursAvant): Collection;

    /** Prêts en retard dont la dernière relance date d'au moins N jours. */
    public function aRelancer(int $intervalle): Collection;
}
