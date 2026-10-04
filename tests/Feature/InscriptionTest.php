<?php

namespace Tests\Feature;

use App\Models\Inscription;
use App\Models\Specialite;
use Database\Seeders\FormationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(FormationSeeder::class);
    }

    private function identite(array $surcharge = []): array
    {
        return $surcharge + [
            'nom' => 'tiwa', 'prenom' => 'grant', 'sexe' => 'M', 'date_naissance' => '2004-07-09',
            'lieu_naissance' => 'bangang', 'pays' => 'Cameroun', 'cni' => '117 369 591',
        ];
    }

    private function remplirJusquaVerification(): void
    {
        $this->post(route('inscription.identite'), $this->identite())->assertRedirect(route('inscription.etape', 'coordonnees'));
        $this->post(route('inscription.coordonnees'), [
            'telephone' => '6 72 51 71 18', 'email' => 'Grant@Example.com', 'nom_mere' => 'sonwa marie', 'contact_parent' => '662250370',
        ])->assertRedirect(route('inscription.etape', 'diplome'));
        $this->post(route('inscription.diplome'), ['diplome' => 'BACC', 'option_diplome' => 'C', 'annee_obtention' => 2023])
            ->assertRedirect(route('inscription.etape', 'formation'));

        $logiciel = Specialite::where('slug', 'genie-logiciel')->first();
        $energie = Specialite::where('slug', 'energies-renouvelables')->first();
        $this->post(route('inscription.formation'), ['choix' => [
            1 => ['filiere' => $logiciel->filiere_id, 'specialite' => $logiciel->id],
            2 => ['filiere' => '', 'specialite' => ''],
            3 => ['filiere' => $energie->filiere_id, 'specialite' => $energie->id],
        ]])->assertRedirect(route('inscription.etape', 'verification'));
    }

    public function test_les_pages_publiques_s_affichent(): void
    {
        foreach (['/', '/acrest', '/technologies-appropriees', '/logement', '/filieres', '/filieres/genie-civil',
            '/specialites', '/specialites/genie-logiciel', '/suivi', '/paiement', '/inscription/identite'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/specialites/inexistante')->assertNotFound();
    }

    public function test_on_ne_peut_pas_sauter_une_etape(): void
    {
        $this->get(route('inscription.etape', 'formation'))->assertRedirect(route('inscription.etape', 'identite'));
    }

    public function test_l_etape_identite_valide_les_champs(): void
    {
        $this->post(route('inscription.identite'), ['nom' => '', 'sexe' => 'X', 'date_naissance' => now()->toDateString(), 'pays' => 'Mars'])
            ->assertSessionHasErrors(['nom', 'sexe', 'date_naissance', 'pays', 'cni', 'lieu_naissance']);
    }

    public function test_une_specialite_doit_appartenir_a_sa_filiere_et_etre_unique(): void
    {
        $this->post(route('inscription.identite'), $this->identite());
        $this->post(route('inscription.coordonnees'), ['telephone' => '672517118', 'email' => 'a@b.cm', 'nom_mere' => 'x', 'contact_parent' => '662250370']);
        $this->post(route('inscription.diplome'), ['diplome' => 'BACC']);

        $batiment = Specialite::where('slug', 'batiment')->first();
        $logiciel = Specialite::where('slug', 'genie-logiciel')->first();

        $this->post(route('inscription.formation'), ['choix' => [
            1 => ['filiere' => $logiciel->filiere_id, 'specialite' => $batiment->id],
            2 => ['filiere' => $batiment->filiere_id, 'specialite' => $batiment->id],
        ]])->assertSessionHasErrors(['choix.1.specialite', 'choix.2.specialite']);
    }

    public function test_parcours_complet_d_inscription(): void
    {
        $this->remplirJusquaVerification();

        $this->get(route('inscription.etape', 'verification'))
            ->assertOk()
            ->assertSee('TIWA')
            ->assertSee('Génie logiciel')
            ->assertSee('Énergies renouvelables');

        $this->post(route('inscription.finaliser'))->assertSessionHasErrors('certifie');

        $reponse = $this->post(route('inscription.finaliser'), ['certifie' => '1']);
        $inscription = Inscription::firstOrFail();

        $reponse->assertRedirect(route('dossier.show', $inscription->code));
        $this->assertMatchesRegularExpression('/^ISAP-\d{2}-[A-Z0-9]{6}$/', $inscription->code);
        $this->assertSame('TIWA', $inscription->nom);
        $this->assertSame('grant@example.com', $inscription->email);
        $this->assertSame('117369591', $inscription->cni);
        $this->assertSame(['Génie logiciel', 'Énergies renouvelables'], $inscription->specialites->pluck('nom')->all());
        $this->assertSame([1, 2], $inscription->specialites->pluck('pivot.rang')->all());

        $this->get(route('dossier.show', $inscription->code))->assertOk()->assertSee($inscription->code);

        // Le formulaire est vidé : une nouvelle inscription repart de zéro
        $this->get(route('inscription.debut'))->assertRedirect(route('inscription.etape', 'identite'));

        // La même pièce d'identité ne peut pas créer un second dossier
        $this->post(route('inscription.identite'), $this->identite())->assertSessionHasErrors('cni');
    }

    public function test_un_dossier_n_est_visible_qu_avec_code_et_email(): void
    {
        $inscription = Inscription::factory()->create(['email' => 'moi@ex.cm']);

        $this->get(route('dossier.show', $inscription->code))->assertRedirect(route('dossier.recherche'));
        $this->post(route('dossier.rechercher'), ['code' => $inscription->code, 'email' => 'autre@ex.cm'])->assertSessionHasErrors('code');
        $this->post(route('dossier.rechercher'), ['code' => strtolower($inscription->code), 'email' => 'MOI@ex.cm'])
            ->assertRedirect(route('dossier.show', $inscription->code));
    }
}
