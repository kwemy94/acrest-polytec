<?php

namespace Tests\Feature;

use App\Enums\NiveauAcces;
use App\Models\Adherent;
use App\Models\Document;
use App\Models\JournalActivite;
use App\Models\RessourceNumerique;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Ressources numériques (§ 13-14) et règle 10 : l'accès respecte les droits de la ressource. */
class RessourcesNumeriquesTest extends TestCase
{
    use RefreshDatabase;

    private Document $document;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Storage::fake('local');
        $this->document = Document::factory()->create(['titre' => 'Cours d\'électricité']);
    }

    private function ajouter(NiveauAcces $niveau, ?UploadedFile $fichier = null): RessourceNumerique
    {
        $this->actingAs(User::factory()->bibliothecaire()->create())
            ->post(route('admin.ressources.store', $this->document), [
                'fichier' => $fichier ?? UploadedFile::fake()->create('cours.pdf', 500, 'application/pdf'),
                'niveau_acces' => $niveau->value,
                'version' => '2',
                'titre' => 'Fichier '.$niveau->libelle(),
            ])->assertSessionHasNoErrors();
        auth()->logout();

        return RessourceNumerique::latest('id')->firstOrFail();
    }

    private function adherent(?Adherent $adherent = null): Adherent
    {
        $adherent ??= Adherent::factory()->create();
        $this->post(route('bibliotheque.connexion.store'), ['matricule' => $adherent->matricule, 'email' => $adherent->email]);

        return $adherent;
    }

    public function test_ajout_stockage_prive_et_tracabilite(): void
    {
        $ressource = $this->ajouter(NiveauAcces::Consultation);

        Storage::disk('local')->assertExists($ressource->fichier);
        $this->assertStringStartsWith('bibliotheque/', $ressource->fichier);
        $this->assertSame('pdf', $ressource->type->value);
        $this->assertSame('2', $ressource->version);
        $this->assertSame(512000, $ressource->taille);
        $this->assertContains('ressource.ajout', JournalActivite::pluck('action')->all());

        // Plusieurs ressources par document, formats variés ; les fichiers non autorisés sont refusés
        $this->ajouter(NiveauAcces::Telechargement, UploadedFile::fake()->create('cours.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'));
        $this->ajouter(NiveauAcces::ConsultationTelechargement, UploadedFile::fake()->create('cours.mp3', 100, 'audio/mpeg'));
        $this->assertSame(['pdf', 'word', 'audio'], $this->document->ressources()->reorder('id')->pluck('type')->map->value->all());

        $this->actingAs(User::factory()->create())->post(route('admin.ressources.store', $this->document), [
            'fichier' => UploadedFile::fake()->create('virus.exe', 10, 'application/x-msdownload'), 'niveau_acces' => 'consultation',
        ])->assertSessionHasErrors('fichier');
        $this->post(route('admin.ressources.store', $this->document), [
            'fichier' => UploadedFile::fake()->create('faux.pdf', 10, 'image/png'), 'niveau_acces' => 'consultation',
        ])->assertSessionHasErrors('fichier');

        // Suppression : fichier effacé et trace conservée
        $this->delete(route('admin.ressources.destroy', $ressource));
        Storage::disk('local')->assertMissing($ressource->fichier);
        $this->assertContains('ressource.suppression', JournalActivite::pluck('action')->all());
    }

    public function test_les_droits_d_acces_sont_respectes(): void
    {
        $consultation = $this->ajouter(NiveauAcces::Consultation);
        $telechargement = $this->ajouter(NiveauAcces::Telechargement);
        $complet = $this->ajouter(NiveauAcces::ConsultationTelechargement);
        $restreint = $this->ajouter(NiveauAcces::Restreint);

        // Visiteur anonyme : aucun accès, pas d'URL publique
        foreach ([$consultation, $complet] as $r) {
            $this->get(route('bibliotheque.ressources.fichier', $r))->assertRedirect(route('bibliotheque.connexion'));
            $this->get(route('bibliotheque.ressources.telecharger', $r))->assertRedirect(route('bibliotheque.connexion'));
        }
        $this->get(route('bibliotheque.show', $this->document))->assertSee('Se connecter pour y accéder')->assertDontSee($restreint->libelle);

        // Adhérent actif
        $this->adherent();
        $this->get(route('bibliotheque.ressources.consulter', $consultation))->assertOk()->assertSee('toolbar=0', false);
        $this->get(route('bibliotheque.ressources.fichier', $consultation))->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Content-Disposition', 'inline; filename=cours-delectricite-v2.pdf');
        $this->get(route('bibliotheque.ressources.telecharger', $consultation))->assertForbidden();

        $this->get(route('bibliotheque.ressources.consulter', $telechargement))->assertForbidden();
        $this->get(route('bibliotheque.ressources.telecharger', $telechargement))->assertDownload('cours-delectricite-v2.pdf');

        $this->get(route('bibliotheque.ressources.consulter', $complet))->assertOk()->assertDontSee('toolbar=0', false);
        $this->get(route('bibliotheque.ressources.telecharger', $complet))->assertDownload();

        $this->get(route('bibliotheque.ressources.fichier', $restreint))->assertForbidden();
        $this->get(route('bibliotheque.ressources.telecharger', $restreint))->assertForbidden();

        // Adhérent suspendu : plus aucun accès
        $this->adherent(Adherent::factory()->suspendu()->create());
        $this->get(route('bibliotheque.ressources.fichier', $consultation))->assertForbidden();
        $this->get(route('bibliotheque.ressources.telecharger', $complet))->assertForbidden();

        // Personnel : accès complet, y compris restreint
        $this->actingAs(User::factory()->bibliothecaire()->create());
        $this->get(route('bibliotheque.ressources.fichier', $restreint))->assertOk();
        $this->get(route('bibliotheque.ressources.telecharger', $consultation))->assertDownload();

        // Modification du niveau d'accès
        $this->patch(route('admin.ressources.update', $consultation), ['niveau_acces' => 'restreint', 'version' => '3'])->assertSessionHas('succes');
        $this->assertSame(NiveauAcces::Restreint, $consultation->fresh()->niveau_acces);
    }

    public function test_un_document_numerique_ouvert_ne_se_demande_pas_en_ligne(): void
    {
        $document = Document::factory()->avecExemplaires(1)->create();
        $this->document = $document;
        $this->ajouter(NiveauAcces::Consultation);

        $this->adherent();
        $this->get(route('bibliotheque.show', $document))->assertSee('Consulter')->assertDontSee('Demander ce document');
        $this->post(route('bibliotheque.demander', $document))->assertSessionHasErrors('emprunt');
    }
}
