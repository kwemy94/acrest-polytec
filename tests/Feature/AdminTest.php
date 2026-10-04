<?php

namespace Tests\Feature;

use App\Models\Inscription;
use App\Models\User;
use Database\Seeders\FormationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_l_administration_est_protegee(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_connexion_et_pages_d_administration(): void
    {
        $this->seed(FormationSeeder::class);
        User::factory()->create(['email' => 'admin@acrest.cm', 'password' => 'secret123']);
        $inscription = Inscription::factory()->create();

        $this->post(route('admin.login.store'), ['email' => 'admin@acrest.cm', 'password' => 'faux'])->assertSessionHasErrors('email');
        $this->post(route('admin.login.store'), ['email' => 'admin@acrest.cm', 'password' => 'secret123'])->assertRedirect(route('admin.dashboard'));

        foreach (['admin.dashboard', 'admin.inscriptions.index', 'admin.paiements.index', 'admin.newsletter.index', 'admin.compte'] as $route) {
            $this->get(route($route))->assertOk();
        }
        $this->get(route('admin.inscriptions.show', $inscription))->assertOk()->assertSee($inscription->code);
        $this->get(route('admin.inscriptions.index', ['q' => $inscription->nom]))->assertSee($inscription->code);

        $csv = $this->get(route('admin.inscriptions.export'))->assertOk()->streamedContent();
        $this->assertStringContainsString($inscription->code, $csv);

        $this->patch(route('admin.inscriptions.statut', $inscription), ['statut' => 'validee'])->assertSessionHas('succes');
        $this->assertSame('validee', $inscription->fresh()->statut->value);
    }

    public function test_newsletter(): void
    {
        $this->post(route('newsletter'), ['email_newsletter' => 'a@b.cm'])->assertSessionHas('newsletter');
        $this->post(route('newsletter'), ['email_newsletter' => 'A@b.cm']);
        $this->assertDatabaseCount('newsletter_abonnes', 1);
    }
}
