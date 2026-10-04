<?php

namespace App\Services\Paiement;

use App\Enums\StatutPaiement;
use App\Exceptions\PaiementException;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Repositories\Contracts\InscriptionRepositoryInterface;
use App\Repositories\Contracts\PaiementRepositoryInterface;
use App\Services\Paiement\Contracts\PasserellePaiementInterface;

class PaiementService
{
    public function __construct(
        private readonly PasserellePaiementInterface $passerelle,
        private readonly InscriptionRepositoryInterface $inscriptions,
        private readonly PaiementRepositoryInterface $paiements,
    ) {
    }

    public function montant(): int
    {
        return (int) config('acrest.paiement.frais_inscription');
    }

    /**
     * @param  array{code:string, operateur:string, telephone:string, reference:string}  $donnees
     *
     * @throws PaiementException
     */
    public function declarer(array $donnees): Paiement
    {
        $inscription = $this->inscriptions->findByCode($donnees['code']);

        if (! $inscription) {
            throw new PaiementException('Aucun dossier ne correspond à ce code d\'inscription. Vérifiez-le sur votre reçu.');
        }

        if ($inscription->estPayee()) {
            throw new PaiementException('Les frais de ce dossier sont déjà réglés. Aucun nouveau paiement n\'est nécessaire.');
        }

        if ($inscription->aPaiementEnCours()) {
            throw new PaiementException('Un paiement est déjà en cours de vérification pour ce dossier. Vous serez informé dès sa confirmation.');
        }

        if ($this->paiements->referenceExiste($donnees['reference'])) {
            throw new PaiementException('Cette référence de transaction a déjà été déclarée.');
        }

        return $this->passerelle->payer($inscription, $this->montant(), $donnees);
    }

    public function valider(Paiement $paiement, int $agentId, ?string $note = null): Paiement
    {
        return $this->paiements->changerStatut($paiement, StatutPaiement::Valide, $agentId, $note);
    }

    public function rejeter(Paiement $paiement, int $agentId, ?string $note = null): Paiement
    {
        return $this->paiements->changerStatut($paiement, StatutPaiement::Rejete, $agentId, $note);
    }

    public function inscription(string $code): ?Inscription
    {
        return $this->inscriptions->findByCode($code);
    }
}
