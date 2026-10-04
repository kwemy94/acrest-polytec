<?php

namespace Tests\Feature;

use App\Enums\StatutPaiement;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaiementTest extends TestCase
{
    use RefreshDatabase;

    private function declarer(Inscription $i, string $reference = 'MP240101.1234.A12345'): \Illuminate\Testing\TestResponse
    {
        return $this->post(route('paiement.store'), [
            'code' => $i->code, 'operateur' => 'mtn_momo', 'telephone' => '6 77 00 00 00', 'reference' => $reference,
        ]);
    }

    public function test_un_candidat_declare_son_paiement(): void
    {
        $inscription = Inscription::factory()->create();

        $this->declarer($inscription)->assertRedirect(route('dossier.show', $inscription->code));

        $paiement = Paiement::firstOrFail();
        $this->assertSame(StatutPaiement::EnAttente, $paiement->statut);
        $this->assertSame((int) config('acrest.paiement.frais_inscription'), $paiement->montant);
        $this->assertSame('677000000', $paiement->telephone);
    }

    public function test_code_inconnu_doublon_et_reference_reutilisee_sont_refuses(): void
    {
        $inscription = Inscription::factory()->create();
        $autre = Inscription::factory()->create();

        $this->post(route('paiement.store'), ['code' => 'ISAP-00-ZZZZZZ', 'operateur' => 'mtn_momo', 'telephone' => '677000000', 'reference' => 'ABCDEF1'])
            ->assertSessionHasErrors('paiement');

        $this->declarer($inscription);
        $this->declarer($inscription, 'AUTREREF1')->assertSessionHasErrors('paiement');
        $this->declarer($autre)->assertSessionHasErrors('paiement');

        $this->assertSame(1, Paiement::count());
    }

    public function test_l_administration_valide_ou_rejette_un_paiement(): void
    {
        $admin = User::factory()->create();
        $inscription = Inscription::factory()->create();
        $this->declarer($inscription);
        $paiement = Paiement::firstOrFail();

        $this->patch(route('admin.paiements.traiter', $paiement), ['decision' => 'valider'])->assertRedirect(route('admin.login'));

        $this->actingAs($admin)
            ->patch(route('admin.paiements.traiter', $paiement), ['decision' => 'rejeter'])
            ->assertSessionHasErrors('note');

        $this->actingAs($admin)->patch(route('admin.paiements.traiter', $paiement), ['decision' => 'valider']);
        $paiement->refresh();
        $this->assertSame(StatutPaiement::Valide, $paiement->statut);
        $this->assertSame($admin->id, $paiement->traite_par);
        $this->assertTrue($inscription->fresh()->estPayee());

        // Un paiement déjà traité ne peut plus changer
        $this->actingAs($admin)->patch(route('admin.paiements.traiter', $paiement), ['decision' => 'rejeter', 'note' => 'x'])
            ->assertSessionHasErrors('paiement');
    }
}
