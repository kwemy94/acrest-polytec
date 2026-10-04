<?php

namespace App\Services\Paiement\Contracts;

use App\Models\Inscription;
use App\Models\Paiement;

/**
 * Passerelle de paiement Mobile Money.
 *
 * L'implémentation par défaut enregistre une déclaration vérifiée ensuite
 * par l'administration. Une passerelle API (MTN MoMo API, Orange Money Web
 * Payment, CinetPay...) peut être branchée en implémentant ce contrat puis
 * en modifiant la liaison dans RepositoryServiceProvider.
 */
interface PasserellePaiementInterface
{
    /**
     * @param  array{operateur:string, telephone:string, reference:string}  $donnees
     */
    public function payer(Inscription $inscription, int $montant, array $donnees): Paiement;
}
