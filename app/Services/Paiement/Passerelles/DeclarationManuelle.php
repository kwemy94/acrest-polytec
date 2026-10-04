<?php

namespace App\Services\Paiement\Passerelles;

use App\Enums\StatutPaiement;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Repositories\Contracts\PaiementRepositoryInterface;
use App\Services\Paiement\Contracts\PasserellePaiementInterface;

/**
 * Le candidat effectue le transfert depuis son téléphone puis déclare
 * la référence de transaction ; un agent la confirme dans l'administration.
 */
class DeclarationManuelle implements PasserellePaiementInterface
{
    public function __construct(private readonly PaiementRepositoryInterface $paiements)
    {
    }

    public function payer(Inscription $inscription, int $montant, array $donnees): Paiement
    {
        /** @var Paiement */
        return $this->paiements->create([
            'inscription_id' => $inscription->id,
            'operateur' => $donnees['operateur'],
            'montant' => $montant,
            'telephone' => $donnees['telephone'],
            'reference' => $donnees['reference'],
            'statut' => StatutPaiement::EnAttente,
        ]);
    }
}
