<?php

namespace Tests\Unit;

use App\Services\Inscription\Etape;
use PHPUnit\Framework\TestCase;

class EtapeTest extends TestCase
{
    public function test_ordre_des_etapes(): void
    {
        $this->assertSame(1, Etape::Identite->numero());
        $this->assertSame(5, Etape::Verification->numero());
        $this->assertNull(Etape::Identite->precedente());
        $this->assertSame(Etape::Coordonnees, Etape::Identite->suivante());
        $this->assertNull(Etape::Verification->suivante());
    }
}
