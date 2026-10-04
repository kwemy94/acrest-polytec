<?php

namespace App\Services\Inscription;

use Illuminate\Contracts\Session\Session;

/**
 * Conserve en session les données saisies à chaque étape du formulaire
 * d'inscription et contrôle l'ordre de progression.
 */
class InscriptionWizard
{
    private const CLE = 'inscription_wizard';

    public function __construct(private readonly Session $session)
    {
    }

    public function enregistrer(Etape $etape, array $donnees): void
    {
        $etat = $this->etat();
        $etat['donnees'][$etape->value] = $donnees;
        $etat['completees'][$etape->value] = true;

        $this->session->put(self::CLE, $etat);
    }

    /** Données d'une étape (ou de toutes si $etape est null). */
    public function donnees(?Etape $etape = null): array
    {
        $donnees = $this->etat()['donnees'];

        return $etape ? ($donnees[$etape->value] ?? []) : $donnees;
    }

    /** Toutes les données fusionnées en un seul tableau. */
    public function toutes(): array
    {
        return array_merge(...array_values($this->donnees() ?: [[]]));
    }

    public function estCompletee(Etape $etape): bool
    {
        return (bool) ($this->etat()['completees'][$etape->value] ?? false);
    }

    /** Première étape non terminée : on ne peut pas aller au-delà. */
    public function premiereIncomplete(): Etape
    {
        foreach (Etape::cases() as $etape) {
            if ($etape !== Etape::Verification && ! $this->estCompletee($etape)) {
                return $etape;
            }
        }

        return Etape::Verification;
    }

    public function estAccessible(Etape $etape): bool
    {
        return $etape->numero() <= $this->premiereIncomplete()->numero();
    }

    public function estPret(): bool
    {
        return $this->premiereIncomplete() === Etape::Verification;
    }

    public function vider(): void
    {
        $this->session->forget(self::CLE);
    }

    private function etat(): array
    {
        return $this->session->get(self::CLE, ['donnees' => [], 'completees' => []]);
    }
}
