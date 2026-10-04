<?php

namespace App\Exceptions;

use RuntimeException;

/** Erreur métier lors d'un paiement : le message est affiché tel quel au candidat. */
class PaiementException extends RuntimeException
{
}
