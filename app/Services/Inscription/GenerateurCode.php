<?php

namespace App\Services\Inscription;

use App\Repositories\Contracts\InscriptionRepositoryInterface;

/**
 * Génère un code d'inscription unique, lisible et facile à dicter
 * (pas de 0/O ni de 1/I), par ex. ISAP-26-K7M4QX.
 */
class GenerateurCode
{
    private const ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public function __construct(private readonly InscriptionRepositoryInterface $inscriptions)
    {
    }

    public function generer(): string
    {
        do {
            $suffixe = '';
            for ($i = 0; $i < 6; $i++) {
                $suffixe .= self::ALPHABET[random_int(0, strlen(self::ALPHABET) - 1)];
            }
            $code = sprintf('ISAP-%s-%s', now()->format('y'), $suffixe);
        } while ($this->inscriptions->codeExiste($code));

        return $code;
    }
}
