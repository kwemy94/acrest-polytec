<?php

namespace App\Services\Inscription;

use App\Enums\StatutInscription;
use App\Mail\InscriptionEnregistree;
use App\Models\Inscription;
use App\Repositories\Contracts\InscriptionRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class InscriptionService
{
    public function __construct(
        private readonly InscriptionRepositoryInterface $inscriptions,
        private readonly GenerateurCode $generateur,
    ) {
    }

    /** Crée le dossier à partir des données rassemblées par le formulaire par étapes. */
    public function finaliser(array $donnees): Inscription
    {
        $choix = collect($donnees['choix'] ?? [])
            ->sortKeys()
            ->pluck('specialite')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $attributs = collect($donnees)->except(['choix'])->all();
        $attributs['code'] = $this->generateur->generer();
        $attributs['statut'] = StatutInscription::EnAttente;

        $inscription = $this->inscriptions->creerAvecChoix($attributs, $choix);

        $this->notifier($inscription);

        return $inscription;
    }

    /** Renvoie le code par e-mail si un dossier correspond (réponse identique sinon). */
    public function renvoyerCode(string $email, string $cni): void
    {
        $inscription = $this->inscriptions->findByEmailEtCni($email, $cni);

        if ($inscription) {
            $this->notifier($inscription);
        }
    }

    private function notifier(Inscription $inscription): void
    {
        try {
            Mail::to($inscription->email)->send(new InscriptionEnregistree($inscription));
        } catch (Throwable $e) {
            // L'envoi d'e-mail ne doit jamais bloquer l'inscription.
            Log::warning('Envoi du mail d\'inscription impossible', ['code' => $inscription->code, 'erreur' => $e->getMessage()]);
        }
    }
}
