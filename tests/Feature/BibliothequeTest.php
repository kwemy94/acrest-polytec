<?php

namespace Tests\Feature;

use App\Enums\EtatExemplaire;
use App\Enums\EvenementEmprunt;
use App\Enums\StatutEmprunt;
use App\Enums\StatutInscription;
use App\Mail\EmpruntNotification;
use App\Mail\NouvelleDemandeEmprunt;
use App\Models\Document;
use App\Models\Emprunt;
use App\Models\Inscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BibliothequeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    private function etudiant(): Inscription
    {
        return Inscription::factory()->create(['statut' => StatutInscription::Validee]);
    }

    private function connecter(Inscription $etudiant): static
    {
        $this->post(route('bibliotheque.connexion.store'), ['code' => $etudiant->code, 'email' => $etudiant->email]);

        return $this;
    }

    private function mailEnvoye(Inscription $etudiant, EvenementEmprunt $evenement): void
    {
        Mail::assertSent(EmpruntNotification::class, fn ($m) => $m->evenement === $evenement && $m->hasTo($etudiant->email));
    }

    public function test_le_catalogue_est_public(): void
    {
        $document = Document::factory()->avecExemplaires(2)->create(['titre' => 'Énergie solaire']);

        $this->get(route('bibliotheque.index', ['q' => 'solaire']))->assertOk()->assertSee('Énergie solaire')->assertSee('2 disponibles sur 2');
        $this->get(route('bibliotheque.show', $document))->assertOk()->assertSee('Se connecter pour emprunter');
    }

    public function test_seuls_les_etudiants_valides_accedent_au_pret(): void
    {
        $candidat = Inscription::factory()->create();
        $this->post(route('bibliotheque.connexion.store'), ['code' => $candidat->code, 'email' => $candidat->email])->assertSessionHasErrors('code');
        $this->post(route('bibliotheque.connexion.store'), ['code' => $candidat->code, 'email' => 'autre@mail.cm'])->assertSessionHasErrors('code');
        $this->get(route('bibliotheque.emprunts'))->assertRedirect(route('bibliotheque.connexion'));

        $etudiant = $this->etudiant();
        $this->post(route('bibliotheque.connexion.store'), ['code' => strtolower($etudiant->code), 'email' => $etudiant->email])
            ->assertRedirect(route('bibliotheque.emprunts'));
        $this->get(route('bibliotheque.emprunts'))->assertOk()->assertSee($etudiant->code);
    }

    public function test_cycle_complet_d_un_emprunt_avec_notifications(): void
    {
        $admin = User::factory()->create();
        $etudiant = $this->etudiant();
        $document = Document::factory()->avecExemplaires(1)->create();

        // Demande
        $this->connecter($etudiant)->post(route('bibliotheque.demander', $document), ['message' => 'Pour mon exposé'])
            ->assertRedirect(route('bibliotheque.emprunts'));
        $emprunt = Emprunt::firstOrFail();
        $this->assertSame(StatutEmprunt::Demande, $emprunt->statut);
        $this->mailEnvoye($etudiant, EvenementEmprunt::Demande);
        Mail::assertSent(NouvelleDemandeEmprunt::class);

        // Doublon refusé
        $this->post(route('bibliotheque.demander', $document))->assertSessionHasErrors('emprunt');

        // Validation : exemplaire mis de côté
        $this->actingAs($admin)->patch(route('admin.emprunts.traiter', [$emprunt, 'valider']))->assertSessionHas('succes');
        $emprunt->refresh();
        $this->assertSame(StatutEmprunt::Reserve, $emprunt->statut);
        $this->assertSame(EtatExemplaire::Reserve, $emprunt->exemplaire->etat);
        $this->assertTrue($emprunt->retirer_avant->isSameDay(today()->addDays(config('acrest.bibliotheque.delai_retrait'))));
        $this->mailEnvoye($etudiant, EvenementEmprunt::Reserve);

        // Remise au guichet
        $this->patch(route('admin.emprunts.traiter', [$emprunt, 'remettre']));
        $emprunt->refresh();
        $this->assertSame(StatutEmprunt::EnCours, $emprunt->statut);
        $this->assertSame(EtatExemplaire::Emprunte, $emprunt->exemplaire->etat);
        $this->assertTrue($emprunt->date_retour_prevue->isSameDay(today()->addDays(config('acrest.bibliotheque.duree_pret'))));
        $this->mailEnvoye($etudiant, EvenementEmprunt::Remis);

        // Prolongation par l'étudiant (une seule autorisée)
        $echeance = $emprunt->date_retour_prevue->copy();
        $this->post(route('bibliotheque.prolonger', $emprunt))->assertSessionHas('succes');
        $this->assertTrue($emprunt->fresh()->date_retour_prevue->isSameDay($echeance->addDays(config('acrest.bibliotheque.duree_pret'))));
        $this->mailEnvoye($etudiant, EvenementEmprunt::Prolonge);
        $this->post(route('bibliotheque.prolonger', $emprunt))->assertSessionHasErrors('emprunt');

        // Retour
        $this->patch(route('admin.emprunts.traiter', [$emprunt, 'retour']));
        $emprunt->refresh();
        $this->assertSame(StatutEmprunt::Rendu, $emprunt->statut);
        $this->assertSame(EtatExemplaire::Disponible, $emprunt->exemplaire->etat);
        $this->mailEnvoye($etudiant, EvenementEmprunt::Rendu);

        // Chaque modèle d'e-mail se génère correctement
        foreach (EvenementEmprunt::cases() as $evenement) {
            $this->assertStringContainsString($document->titre, (new EmpruntNotification($emprunt, $evenement))->render());
        }
        $this->assertStringContainsString($etudiant->code, (new NouvelleDemandeEmprunt($emprunt))->render());
    }

    public function test_un_exemplaire_rendu_est_reserve_pour_la_file_d_attente(): void
    {
        $admin = User::factory()->create();
        $premier = $this->etudiant();
        $second = $this->etudiant();
        $document = Document::factory()->avecExemplaires(1)->create();

        $this->connecter($premier)->post(route('bibliotheque.demander', $document));
        $emprunt = Emprunt::firstOrFail();
        $this->actingAs($admin)->patch(route('admin.emprunts.traiter', [$emprunt, 'remettre']));

        $this->connecter($second)->post(route('bibliotheque.demander', $document));
        $attente = Emprunt::where('inscription_id', $second->id)->firstOrFail();

        // Plus d'exemplaire : la demande reste en file d'attente
        $this->actingAs($admin)->patch(route('admin.emprunts.traiter', [$attente, 'valider']))->assertSessionHasErrors('emprunt');

        // Personne ne peut prolonger un document attendu
        $this->connecter($premier)->post(route('bibliotheque.prolonger', $emprunt))->assertSessionHasErrors('emprunt');

        $this->actingAs($admin)->patch(route('admin.emprunts.traiter', [$emprunt, 'retour']));

        $attente->refresh();
        $this->assertSame(StatutEmprunt::Reserve, $attente->statut);
        $this->assertSame($emprunt->fresh()->exemplaire_id, $attente->exemplaire_id);
        $this->assertSame(EtatExemplaire::Reserve, $attente->exemplaire->etat);
        $this->mailEnvoye($second, EvenementEmprunt::Reserve);
    }

    public function test_quota_retard_et_consultation_sur_place(): void
    {
        config(['acrest.bibliotheque.max_emprunts' => 1]);
        $etudiant = $this->etudiant();
        [$a, $b] = Document::factory()->avecExemplaires(1)->count(2)->create();
        $surPlace = Document::factory()->avecExemplaires(1)->create(['consultation_sur_place' => true]);

        $this->connecter($etudiant)->post(route('bibliotheque.demander', $surPlace))->assertSessionHasErrors('emprunt');
        $this->post(route('bibliotheque.demander', $a))->assertSessionHas('succes');
        $this->post(route('bibliotheque.demander', $b))->assertSessionHasErrors('emprunt');

        // Un prêt en retard bloque toute nouvelle demande
        config(['acrest.bibliotheque.max_emprunts' => 5]);
        Emprunt::first()->update(['statut' => StatutEmprunt::EnCours, 'date_retour_prevue' => today()->subDay()]);
        $this->post(route('bibliotheque.demander', $b))->assertSessionHasErrors('emprunt');
    }

    public function test_un_etudiant_n_agit_que_sur_ses_emprunts(): void
    {
        $proprietaire = $this->etudiant();
        $document = Document::factory()->avecExemplaires(1)->create();
        $this->connecter($proprietaire)->post(route('bibliotheque.demander', $document));
        $emprunt = Emprunt::firstOrFail();

        $this->connecter($this->etudiant())->post(route('bibliotheque.annuler', $emprunt))->assertNotFound();

        $this->connecter($proprietaire)->post(route('bibliotheque.annuler', $emprunt))->assertSessionHas('succes');
        $this->assertSame(StatutEmprunt::Annule, $emprunt->fresh()->statut);
    }

    public function test_la_tache_quotidienne_expire_rappelle_et_relance(): void
    {
        $etudiant = $this->etudiant();
        $documents = Document::factory()->avecExemplaires(1)->count(3)->create();
        [$reservation, $bientot, $retard] = $documents->map(fn (Document $d) => Emprunt::create([
            'inscription_id' => $etudiant->id, 'document_id' => $d->id, 'exemplaire_id' => $d->exemplaires->first()->id,
            'statut' => StatutEmprunt::EnCours, 'date_pret' => now()->subDays(10),
        ]));
        $reservation->update(['statut' => StatutEmprunt::Reserve, 'retirer_avant' => today()->subDay()]);
        $reservation->exemplaire->update(['etat' => EtatExemplaire::Reserve]);
        $bientot->update(['date_retour_prevue' => today()->addDay()]);
        $retard->update(['date_retour_prevue' => today()->subDays(4)]);

        $this->artisan('bibliotheque:echeances')->assertSuccessful();

        $this->assertSame(StatutEmprunt::Annule, $reservation->fresh()->statut);
        $this->assertSame(EtatExemplaire::Disponible, $reservation->exemplaire->fresh()->etat);
        $this->assertNotNull($bientot->fresh()->rappel_envoye_le);
        $this->assertNotNull($retard->fresh()->derniere_relance_le);
        foreach ([EvenementEmprunt::Expire, EvenementEmprunt::Rappel, EvenementEmprunt::Retard] as $evenement) {
            $this->mailEnvoye($etudiant, $evenement);
        }

        // Pas de nouvel envoi le même jour
        Mail::fake();
        $this->artisan('bibliotheque:echeances');
        Mail::assertNothingSent();
    }

    public function test_administration_du_catalogue_et_pret_au_guichet(): void
    {
        $admin = User::factory()->create();
        $etudiant = $this->etudiant();
        $this->actingAs($admin);

        $this->post(route('admin.documents.store'), [
            'titre' => 'Machines électriques', 'auteurs' => 'T. Wildi', 'type' => 'livre', 'langue' => 'fr', 'exemplaires' => 2, 'isbn' => '978-2-10-000000-0',
        ])->assertSessionHasNoErrors();
        $document = Document::firstOrFail();
        $this->assertSame('9782100000000', $document->isbn);
        $this->assertCount(2, $document->exemplaires);
        $code = $document->exemplaires->first()->code;

        foreach (['admin.documents.index', 'admin.emprunts.index', 'admin.emprunts.create', 'admin.dashboard'] as $route) {
            $this->get(route($route))->assertOk();
        }
        $this->get(route('admin.documents.show', $document))->assertOk()->assertSee($code);

        $this->post(route('admin.emprunts.store'), ['code' => $etudiant->code, 'exemplaire' => strtolower($code)])->assertSessionHas('succes');
        $emprunt = Emprunt::firstOrFail();
        $this->assertSame(StatutEmprunt::EnCours, $emprunt->statut);
        $this->mailEnvoye($etudiant, EvenementEmprunt::Remis);

        // Exemplaire déjà prêté
        $this->post(route('admin.emprunts.store'), ['code' => $this->etudiant()->code, 'exemplaire' => $code])->assertSessionHasErrors('emprunt');

        // Une langue hors liste est refusée
        $this->post(route('admin.documents.store'), ['titre' => 'X', 'auteurs' => 'Y', 'type' => 'livre', 'langue' => 'klingon', 'exemplaires' => 0])
            ->assertSessionHasErrors('langue');

        // Un document avec historique ne peut pas être supprimé
        $this->delete(route('admin.documents.destroy', $document))->assertSessionHasErrors('document');
        $this->get(route('admin.emprunts.index', ['q' => $etudiant->code]))->assertSee($document->titre);
    }

    private function ajouterPdf(array $champs = []): Document
    {
        $this->actingAs(User::factory()->create())->post(route('admin.documents.store'), [
            'titre' => 'Renewable Energy', 'auteurs' => 'G. Boyle', 'type' => 'livre', 'langue' => 'en', 'exemplaires' => 0,
            'fichier' => UploadedFile::fake()->create('boyle.pdf', 800, 'application/pdf'),
            ...$champs,
        ])->assertSessionHasNoErrors();
        auth()->logout();

        return Document::latest('id')->firstOrFail();
    }

    public function test_langue_et_version_numerique_au_catalogue(): void
    {
        Storage::fake('local');
        $document = $this->ajouterPdf();
        Document::factory()->avecExemplaires(1)->create(['titre' => 'Livre en français']);

        $this->assertTrue($document->estNumerique());
        $this->assertFalse($document->telechargeable);
        Storage::disk('local')->assertExists($document->fichier);

        $this->get(route('bibliotheque.index', ['langue' => 'en']))->assertSee('Renewable Energy')->assertDontSee('Livre en français');
        $this->get(route('bibliotheque.index', ['numerique' => 1]))->assertSee('Renewable Energy')->assertDontSee('Livre en français');
        $this->get(route('bibliotheque.show', $document))->assertSee('Anglais')->assertSee('Se connecter pour lire')
            ->assertDontSee('Indisponible')->assertDontSee('Exemplaires papier');

        // Un PDF n'est pas une image ou un exécutable déguisé
        $this->actingAs(User::factory()->create())->post(route('admin.documents.store'), [
            'titre' => 'X', 'auteurs' => 'Y', 'type' => 'livre', 'langue' => 'fr', 'exemplaires' => 0,
            'fichier' => UploadedFile::fake()->image('photo.jpg'),
        ])->assertSessionHasErrors('fichier');
    }

    public function test_lecture_en_ligne_et_telechargement_controles(): void
    {
        Storage::fake('local');
        $document = $this->ajouterPdf();

        // Visiteur non connecté : renvoyé vers la connexion, aucun accès au fichier
        $this->get(route('bibliotheque.lire', $document))->assertRedirect(route('bibliotheque.connexion'));
        $this->get(route('bibliotheque.pdf', $document))->assertRedirect(route('bibliotheque.connexion'));
        $this->get(route('bibliotheque.telecharger', $document))->assertRedirect(route('bibliotheque.connexion'));

        // Étudiant connecté : lecture en ligne seulement
        $this->connecter($this->etudiant());
        $this->get(route('bibliotheque.lire', $document))->assertOk()->assertSee('toolbar=0', false)->assertSee('Lecture en ligne uniquement');
        $this->get(route('bibliotheque.pdf', $document))->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Content-Disposition', 'inline; filename=renewable-energy.pdf');
        $this->get(route('bibliotheque.telecharger', $document))->assertForbidden();

        // Téléchargement autorisé
        $document->update(['telechargeable' => true]);
        $this->get(route('bibliotheque.telecharger', $document))->assertDownload('renewable-energy.pdf');

        // Document sans PDF
        $papier = Document::factory()->avecExemplaires(1)->create();
        $this->get(route('bibliotheque.lire', $papier))->assertNotFound();

        // Même avec des exemplaires papier, un document numérique ne se demande pas en prêt
        $this->actingAs(User::factory()->create())->post(route('admin.documents.exemplaires.store', $document), ['nombre' => 1]);
        $this->get(route('bibliotheque.show', $document))->assertSee('Lire en ligne')->assertDontSee('Exemplaires papier');
        $this->post(route('bibliotheque.demander', $document))->assertSessionHasErrors('emprunt');
        $this->assertSame(0, Emprunt::count());
    }

    public function test_remplacement_et_suppression_du_pdf(): void
    {
        Storage::fake('local');
        $document = $this->ajouterPdf(['telechargeable' => 1]);
        $ancien = $document->fichier;
        $admin = User::factory()->create();
        $notice = ['titre' => $document->titre, 'auteurs' => $document->auteurs, 'type' => 'livre', 'langue' => 'en'];

        $this->actingAs($admin)->put(route('admin.documents.update', $document), [
            ...$notice, 'telechargeable' => 1, 'fichier' => UploadedFile::fake()->create('v2.pdf', 300, 'application/pdf'),
        ])->assertSessionHasNoErrors();
        $document->refresh();
        Storage::disk('local')->assertMissing($ancien);
        Storage::disk('local')->assertExists($document->fichier);

        // L'administrateur peut toujours télécharger
        $document->update(['telechargeable' => false]);
        $this->get(route('bibliotheque.telecharger', $document))->assertDownload();

        $this->put(route('admin.documents.update', $document), [...$notice, 'supprimer_fichier' => 1]);
        $fichier = $document->fichier;
        $document->refresh();
        $this->assertNull($document->fichier);
        Storage::disk('local')->assertMissing($fichier);

        // La suppression du document efface aussi son PDF
        $autre = $this->ajouterPdf();
        $this->actingAs($admin)->delete(route('admin.documents.destroy', $autre));
        Storage::disk('local')->assertMissing($autre->fichier);
    }
}
