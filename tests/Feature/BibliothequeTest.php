<?php

namespace Tests\Feature;

use App\Enums\EvenementEmprunt;
use App\Enums\StatutAdherent;
use App\Enums\StatutEmprunt;
use App\Enums\StatutExemplaire;
use App\Enums\StatutInscription;
use App\Enums\StatutPaiement;
use App\Enums\TypeAdherent;
use App\Enums\TypeIncident;
use App\Enums\TypeLocalisation;
use App\Mail\EmpruntNotification;
use App\Mail\NouvelleDemandeEmprunt;
use App\Models\Adherent;
use App\Models\Document;
use App\Models\Emprunt;
use App\Models\Exemplaire;
use App\Models\Incident;
use App\Models\Inscription;
use App\Models\JournalActivite;
use App\Models\Localisation;
use App\Models\Paiement;
use App\Models\TypeDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BibliothequeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    private function bibliothecaire(): User
    {
        return User::factory()->bibliothecaire()->create();
    }

    /** Salle Sciences → Rayon Informatique → Étagère A → Niveau 3 */
    private function niveau3(): Localisation
    {
        $parent = null;
        foreach ([['Salle Sciences', TypeLocalisation::Salle], ['Rayon Informatique', TypeLocalisation::Rayon], ['Étagère A', TypeLocalisation::Etagere], ['Niveau 3', TypeLocalisation::Niveau]] as [$nom, $type]) {
            $parent = Localisation::create(['nom' => $nom, 'type' => $type, 'parent_id' => $parent?->id]);
        }

        return $parent;
    }

    /** Étudiant en règle : dossier validé et frais d'inscription payés. */
    private function etudiantEnRegle(array $attributs = []): Inscription
    {
        $inscription = Inscription::factory()->create([...$attributs, 'statut' => StatutInscription::Validee]);
        Paiement::create([
            'inscription_id' => $inscription->id, 'operateur' => 'mtn_momo', 'montant' => 25000,
            'telephone' => '677000000', 'reference' => 'REF'.$inscription->id, 'statut' => StatutPaiement::Valide,
        ]);

        return $inscription;
    }

    private function preter(Adherent $adherent, Exemplaire|string $exemplaire)
    {
        return $this->post(route('admin.emprunts.store'), [
            'matricule' => $adherent->matricule,
            'exemplaire' => $exemplaire instanceof Exemplaire ? $exemplaire->code_inventaire : $exemplaire,
        ]);
    }

    private function mailEnvoye(Adherent $adherent, EvenementEmprunt $evenement): void
    {
        Mail::assertSent(EmpruntNotification::class, fn ($m) => $m->evenement === $evenement && $m->hasTo($adherent->email));
    }

    /* ---------------------------------------------------------------
     | Critère de réussite du MVP (§ 23), de bout en bout
     * ------------------------------------------------------------- */

    public function test_scenario_mvp_de_bout_en_bout(): void
    {
        $this->actingAs($this->bibliothecaire());
        $niveau = $this->niveau3();
        $livre = TypeDocument::where('nom', 'Livre')->firstOrFail();

        // 1-4. Document, auteurs, 3 exemplaires localisés
        $this->post(route('admin.documents.store'), [
            'titre' => 'Introduction à l\'intelligence artificielle',
            'auteurs' => "Stuart Russell\nPeter Norvig",
            'type_document_id' => $livre->id,
            'langue' => 'fr',
            'isbn' => '978-2-7440-7455-4',
            'mots_cles' => 'IA, apprentissage, IA',
            'exemplaires' => 3,
            'localisation_id' => $niveau->id,
            'source_acquisition' => 'Achat',
        ])->assertSessionHasNoErrors();

        $document = Document::firstOrFail();
        $this->assertSame(['Stuart Russell', 'Peter Norvig'], $document->auteurs->pluck('nom')->all());
        $this->assertSame('IA, apprentissage', $document->mots_cles);
        $this->assertSame(['INV-00001', 'INV-00002', 'INV-00003'], $document->exemplaires->pluck('code_inventaire')->all());
        $this->assertSame('Salle Sciences → Rayon Informatique → Étagère A → Niveau 3', $document->exemplaires->first()->localisation->chemin);

        // 5-6. Recherche par auteur : disponibilité, nombre d'exemplaires, localisation
        $this->get(route('admin.documents.index', ['q' => 'Norvig']))->assertOk()
            ->assertSee('Introduction à l')->assertSee('3 disponibles sur 3')->assertSee('Rayon Informatique / Étagère A');
        $this->get(route('bibliotheque.index', ['q' => 'apprentissage']))->assertSee('Introduction à l');

        // Adhérent
        $etudiant = $this->etudiantEnRegle(['nom' => 'TIWA', 'prenom' => 'Grant', 'email' => 'grant@exemple.cm']);
        $this->post(route('admin.adherents.store'), [
            'type' => 'etudiant', 'inscription_id' => $etudiant->id, 'date_inscription' => today()->toDateString(), 'statut' => 'actif',
        ])->assertSessionHasNoErrors();
        $adherent = Adherent::where('matricule', $etudiant->code)->firstOrFail();
        $this->assertSame('TIWA', $adherent->nom);

        // 7-8. Prêt : l'exemplaire passe « emprunté »
        $this->preter($adherent, 'inv-00002')->assertSessionHas('succes');
        $emprunt = Emprunt::firstOrFail();
        $this->assertSame(StatutEmprunt::EnCours, $emprunt->statut);
        $this->assertSame(StatutExemplaire::Emprunte, $emprunt->exemplaire->statut);
        $this->assertTrue($emprunt->date_retour_prevue->isSameDay(today()->addDays(14)));
        $this->mailEnvoye($adherent, EvenementEmprunt::Remis);

        // 9. Prêts en cours
        $this->get(route('admin.emprunts.index', ['statut' => 'en_cours']))->assertOk()->assertSee('INV-00002')->assertSee('TIWA Grant');

        // 10-11. Retour : l'exemplaire redevient disponible
        $this->get(route('admin.emprunts.retours', ['code' => 'INV-00002']))->assertOk()->assertSee('Enregistrer le retour');
        $this->patch(route('admin.emprunts.retour', $emprunt), ['etat_physique' => 'bon'])->assertSessionHas('succes');
        $emprunt->refresh();
        $this->assertSame(StatutEmprunt::Rendu, $emprunt->statut);
        $this->assertSame(0, $emprunt->jours_retard);
        $this->assertSame(StatutExemplaire::Disponible, $emprunt->exemplaire->statut);
        $this->mailEnvoye($adherent, EvenementEmprunt::Rendu);

        // 12. Historique
        $actions = JournalActivite::pluck('action')->all();
        foreach (['document.creation', 'exemplaire.ajout', 'adherent.creation', 'pret', 'retour'] as $action) {
            $this->assertContains($action, $actions);
        }
        $this->assertSame($this->app['auth']->id(), JournalActivite::where('action', 'pret')->first()->user_id);
        $this->get(route('admin.journal', ['action' => 'retour']))->assertOk()->assertSee('Retour de INV-00002');
        $this->get(route('admin.exemplaires.show', $emprunt->exemplaire))->assertOk()->assertSee('Niveau 3')->assertSee('TIWA Grant');
        $this->get(route('admin.documents.show', $document))->assertOk()->assertSee('Prêt de INV-00002');
        $this->get(route('admin.adherents.show', $adherent))->assertOk()->assertSee('Introduction à l');
        $this->get(route('admin.bibliotheque'))->assertOk();
    }

    /* ---------------------------------------------------------------
     | Règles métier (§ 18)
     * ------------------------------------------------------------- */

    public function test_regles_de_pret(): void
    {
        $this->actingAs($this->bibliothecaire());
        $document = Document::factory()->avecExemplaires(3)->create();
        [$ex1, $ex2, $ex3] = $document->exemplaires;
        $adherent = Adherent::factory()->create();

        // Adhérent suspendu ou expiré (règle 5)
        $this->preter(Adherent::factory()->suspendu()->create(), $ex1)->assertSessionHasErrors('emprunt');
        $this->preter(Adherent::factory()->expire()->create(), $ex1)->assertSessionHasErrors('emprunt');

        // Exemplaire perdu, en réparation ou retiré (règles 3 et 8)
        foreach ([StatutExemplaire::Perdu, StatutExemplaire::EnReparation, StatutExemplaire::Retire] as $statut) {
            $ex1->update(['statut' => $statut]);
            $this->preter($adherent, $ex1)->assertSessionHasErrors('emprunt');
        }
        $ex1->update(['statut' => StatutExemplaire::Disponible]);

        // Un seul prêt actif par exemplaire (règle 4)
        $this->preter($adherent, $ex1)->assertSessionHas('succes');
        $this->preter(Adherent::factory()->create(), $ex1)->assertSessionHasErrors('emprunt');
        $this->assertSame(1, Emprunt::where('exemplaire_actif_id', $ex1->id)->count());

        // Un même document ne se prête pas deux fois au même adhérent
        $this->preter($adherent, $ex2)->assertSessionHasErrors('emprunt');

        // Limite de prêts selon le type d'adhérent (règle 6)
        $this->actingAs(User::factory()->create())->put(route('admin.parametres.update'), [
            'max_prolongations' => 1, 'delai_retrait' => 3, 'rappel_avant_echeance' => 2, 'relance_tous_les' => 3,
            'types' => collect(TypeAdherent::cases())->mapWithKeys(fn ($t) => [$t->value => ['duree' => $t === TypeAdherent::Enseignant ? 30 : 14, 'max' => 1]])->all(),
        ])->assertSessionHasNoErrors();
        $autre = Document::factory()->avecExemplaires(1)->create();
        $this->preter($adherent, $autre->exemplaires->first())->assertSessionHasErrors('emprunt');

        $enseignant = Adherent::factory()->type(TypeAdherent::Enseignant)->create();
        $this->preter($enseignant, $ex3)->assertSessionHas('succes');
        $this->assertTrue(Emprunt::latest('id')->first()->date_retour_prevue->isSameDay(today()->addDays(30)));

        // Un prêt en retard bloque tout nouvel emprunt
        Emprunt::where('adherent_id', $enseignant->id)->update(['date_retour_prevue' => today()->subDay()]);
        $this->preter($enseignant, $autre->exemplaires->first())->assertSessionHasErrors('emprunt');

        // Consultation sur place
        $surPlace = Document::factory()->avecExemplaires(1)->create(['consultation_sur_place' => true]);
        $this->preter(Adherent::factory()->create(), $surPlace->exemplaires->first())->assertSessionHasErrors('emprunt');
    }

    public function test_retour_endommage_en_retard_et_perte(): void
    {
        $this->actingAs($this->bibliothecaire());
        $document = Document::factory()->avecExemplaires(2)->create();
        [$ex1, $ex2] = $document->exemplaires;
        $adherent = Adherent::factory()->type(TypeAdherent::Enseignant)->create();

        $this->preter($adherent, $ex1);
        $emprunt = Emprunt::firstOrFail();
        $emprunt->update(['date_retour_prevue' => today()->subDays(3)]);
        $this->get(route('admin.emprunts.index', ['retard' => 1]))->assertSee($ex1->code_inventaire)->assertSee('3 j de retard');

        // Retour endommagé et en retard (règle 7)
        $this->patch(route('admin.emprunts.retour', $emprunt), ['etat_physique' => 'endommage', 'note' => 'Couverture déchirée']);
        $emprunt->refresh();
        $this->assertSame(3, $emprunt->jours_retard);
        $this->assertSame(StatutExemplaire::EnReparation, $ex1->fresh()->statut);
        $this->assertSame('endommage', $ex1->fresh()->etat_physique->value);
        $this->assertEqualsCanonicalizing([TypeIncident::Dommage, TypeIncident::Retard], Incident::pluck('type')->all());

        // Retour avec retrait du catalogue
        $this->preter($adherent, $ex2);
        $this->patch(route('admin.emprunts.retour', Emprunt::latest('id')->first()), ['etat_physique' => 'moyen', 'retirer' => 1]);
        $this->assertSame(StatutExemplaire::Retire, $ex2->fresh()->statut);

        // Perte pendant un prêt : l'exemplaire ne peut plus être prêté (règle 8)
        $ex1->refresh()->update(['statut' => StatutExemplaire::Disponible]);
        $this->preter($adherent, $ex1)->assertSessionHas('succes');
        $pret = Emprunt::latest('id')->first();
        $this->patch(route('admin.emprunts.perte', $pret), ['note' => 'Perdu en stage'])->assertSessionHas('succes');
        $this->assertSame(StatutEmprunt::Perdu, $pret->fresh()->statut);
        $this->assertSame(StatutExemplaire::Perdu, $ex1->fresh()->statut);
        $this->assertNull($pret->fresh()->exemplaire_actif_id);
        $this->preter(Adherent::factory()->create(), $ex1)->assertSessionHasErrors('emprunt');
    }

    public function test_localisations_et_historique_des_deplacements(): void
    {
        $this->actingAs($this->bibliothecaire());
        $niveau = $this->niveau3();
        $magasin = Localisation::create(['nom' => 'Magasin', 'type' => TypeLocalisation::Magasin]);
        $exemplaire = Document::factory()->avecExemplaires(1, ['localisation_id' => $niveau->id])->create()->exemplaires->first();

        $this->patch(route('admin.exemplaires.deplacer', $exemplaire), ['localisation_id' => $magasin->id, 'motif' => 'Désherbage'])->assertSessionHas('succes');
        $this->patch(route('admin.exemplaires.deplacer', $exemplaire), ['localisation_id' => $niveau->id]);

        $historique = $exemplaire->historiqueLocalisations()->reorder('id')->get();
        $this->assertCount(3, $historique);
        $this->assertSame([null, $niveau->id, $magasin->id], $historique->pluck('ancienne_localisation_id')->all());
        $this->assertSame([$niveau->id, $magasin->id, $niveau->id], $historique->pluck('nouvelle_localisation_id')->all());
        $this->get(route('admin.exemplaires.show', $exemplaire))->assertSee('Désherbage')->assertSee('Magasin');

        // Recherche d'exemplaires dans un espace et ses sous-espaces
        $salle = Localisation::where('nom', 'Salle Sciences')->first();
        $this->get(route('admin.exemplaires.index', ['localisation' => $salle->id]))->assertSee($exemplaire->code_inventaire);
        $this->get(route('admin.localisations.show', $salle))->assertOk()->assertSee($exemplaire->code_inventaire);

        // Un espace ne peut pas être rangé dans son propre sous-espace, ni supprimé s'il n'est pas vide
        $this->put(route('admin.localisations.update', $salle), ['nom' => 'Salle Sciences', 'type' => 'salle', 'parent_id' => $niveau->id])->assertSessionHasErrors('parent_id');
        $this->delete(route('admin.localisations.destroy', $niveau))->assertSessionHasErrors('localisation');
        $this->assertContains('exemplaire.localisation', JournalActivite::pluck('action')->all());

        // Statut manuel : réparation puis remise en rayon, impossible pendant un prêt
        $this->patch(route('admin.exemplaires.statut', $exemplaire), ['statut' => 'en_reparation'])->assertSessionHas('succes');
        $this->patch(route('admin.exemplaires.statut', $exemplaire), ['statut' => 'disponible']);
        $this->preter(Adherent::factory()->create(), $exemplaire);
        $this->patch(route('admin.exemplaires.statut', $exemplaire), ['statut' => 'perdu'])->assertSessionHasErrors('exemplaire');
    }

    /* ---------------------------------------------------------------
     | Rôles et administration
     * ------------------------------------------------------------- */

    public function test_roles_administrateur_et_bibliothecaire(): void
    {
        $bibliothecaire = $this->bibliothecaire();
        $this->actingAs($bibliothecaire);

        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.bibliotheque'));
        foreach (['admin.bibliotheque', 'admin.documents.index', 'admin.documents.create', 'admin.exemplaires.index', 'admin.localisations.index',
            'admin.adherents.index', 'admin.adherents.create', 'admin.emprunts.index', 'admin.emprunts.create', 'admin.emprunts.retours', 'admin.journal', 'admin.compte'] as $route) {
            $this->get(route($route))->assertOk();
        }
        foreach (['admin.inscriptions.index', 'admin.paiements.index', 'admin.utilisateurs.index', 'admin.referentiels'] as $route) {
            $this->get(route($route))->assertForbidden();
        }

        // L'administrateur gère les comptes, les référentiels et les paramètres
        $admin = User::factory()->create();
        $this->actingAs($admin);
        $this->get(route('admin.referentiels'))->assertOk()->assertSee('Thèse');
        $this->post(route('admin.utilisateurs.store'), [
            'name' => 'Marie Biblio', 'email' => 'marie@acrest.cm', 'role' => 'bibliothecaire', 'actif' => 1,
            'password' => 'motdepasse1', 'password_confirmation' => 'motdepasse1',
        ])->assertSessionHasNoErrors();
        $this->post(route('admin.types-documents.store'), ['nom' => 'Atlas'])->assertSessionHasNoErrors();
        $this->post(route('admin.categories.store'), ['nom' => 'Hydraulique'])->assertSessionHasNoErrors();
        $this->put(route('admin.utilisateurs.update', $admin), ['name' => $admin->name, 'email' => $admin->email, 'role' => 'bibliothecaire', 'actif' => 1])
            ->assertSessionHasErrors('role');

        // Un compte désactivé ne peut plus se connecter
        auth()->logout();
        User::where('email', 'marie@acrest.cm')->update(['actif' => false]);
        $this->post(route('admin.login.store'), ['email' => 'marie@acrest.cm', 'password' => 'motdepasse1'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_menu_lateral_selon_l_espace(): void
    {
        // Administration générale : un simple lien « Bibliothèque », pas le détail de la bibliothèque
        $this->actingAs(User::factory()->create());
        $this->get(route('admin.dashboard'))->assertOk()
            ->assertSee(route('admin.bibliotheque'))->assertSee('Inscriptions')
            ->assertDontSee('Nouveau prêt')->assertDontSee('Administration générale');

        // Espace bibliothèque : son propre menu et un retour vers l'administration générale
        $this->get(route('admin.bibliotheque'))->assertOk()
            ->assertSee('Nouveau prêt')->assertSee('Localisations')->assertSee('Types, catégories, prêt')
            ->assertSee('Administration générale')->assertDontSee('Newsletter');

        // Bibliothécaire : toujours le menu de la bibliothèque, sans retour ni réglages d'administrateur
        $this->actingAs($this->bibliothecaire());
        $this->get(route('admin.compte'))->assertOk()
            ->assertSee('Nouveau prêt')->assertSee('Mon profil')
            ->assertDontSee('Administration générale')->assertDontSee('Types, catégories, prêt')->assertDontSee('Inscriptions');
    }

    public function test_creation_d_un_etudiant_parmi_les_etudiants_en_regle(): void
    {
        $this->actingAs($this->bibliothecaire());
        $enRegle = $this->etudiantEnRegle(['nom' => 'KAMGA', 'prenom' => 'Aline', 'email' => 'aline@exemple.cm', 'telephone' => '699112233']);
        $nonPaye = Inscription::factory()->create(['nom' => 'NONPAYE', 'statut' => StatutInscription::Validee]);
        $enAttente = Inscription::factory()->create(['nom' => 'ENATTENTE']);
        $creer = fn (array $donnees) => $this->post(route('admin.adherents.store'), [
            'type' => 'etudiant', 'date_inscription' => today()->toDateString(), 'statut' => 'actif', ...$donnees,
        ]);

        // La liste ne propose que les étudiants en règle
        $this->get(route('admin.adherents.create'))->assertOk()
            ->assertSee('KAMGA Aline')->assertDontSee('NONPAYE')->assertDontSee('ENATTENTE');

        // Un étudiant doit être choisi dans la liste ; la saisie manuelle et les dossiers non en règle sont refusés
        $creer(['matricule' => 'X1', 'nom' => 'Manuel'])->assertSessionHasErrors('inscription_id');
        $creer(['inscription_id' => $nonPaye->id])->assertSessionHasErrors('inscription_id');
        $creer(['inscription_id' => $enAttente->id])->assertSessionHasErrors('inscription_id');

        // Les informations sont reprises du dossier, même si d'autres valeurs sont envoyées
        $creer(['inscription_id' => $enRegle->id, 'nom' => 'Pirate', 'matricule' => 'FAUX'])->assertSessionHasNoErrors();
        $adherent = Adherent::sole();
        $this->assertSame([$enRegle->code, 'KAMGA', 'Aline', 'aline@exemple.cm', '699112233', $enRegle->id],
            [$adherent->matricule, $adherent->nom, $adherent->prenom, $adherent->email, $adherent->telephone, $adherent->inscription_id]);

        // Un étudiant déjà adhérent n'est plus proposé ni accepté
        $this->get(route('admin.adherents.create'))->assertDontSee('KAMGA Aline');
        $creer(['inscription_id' => $enRegle->id])->assertSessionHasErrors('inscription_id');

        // Les autres types d'adhérents restent en saisie manuelle
        $this->post(route('admin.adherents.store'), [
            'type' => 'enseignant', 'matricule' => 'ens-01', 'nom' => 'Fotso', 'date_inscription' => today()->toDateString(), 'statut' => 'actif',
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('adherents', ['matricule' => 'ENS-01', 'nom' => 'FOTSO', 'inscription_id' => null]);
    }

    public function test_import_des_etudiants_en_regle(): void
    {
        $this->actingAs($this->bibliothecaire());
        $enRegle = $this->etudiantEnRegle();
        Inscription::factory()->create(['statut' => StatutInscription::Validee]); // validé mais non payé
        Inscription::factory()->create();

        $this->post(route('admin.adherents.importer'))->assertSessionHas('succes');
        $this->post(route('admin.adherents.importer'))->assertSessionHas('info');

        $adherent = Adherent::sole();
        $this->assertSame($enRegle->code, $adherent->matricule);
        $this->assertSame(TypeAdherent::Etudiant, $adherent->type);
        $this->assertSame($enRegle->id, $adherent->inscription_id);
    }

    /* ---------------------------------------------------------------
     | Espace adhérent : demandes en ligne et file d'attente
     * ------------------------------------------------------------- */

    public function test_demande_en_ligne_et_file_d_attente(): void
    {
        $document = Document::factory()->avecExemplaires(1)->create();
        $premier = Adherent::factory()->create();
        $second = Adherent::factory()->create();
        $connecter = fn (Adherent $a) => $this->post(route('bibliotheque.connexion.store'), ['matricule' => strtolower($a->matricule), 'email' => strtoupper($a->email)]);

        $this->post(route('bibliotheque.connexion.store'), ['matricule' => $premier->matricule, 'email' => 'faux@exemple.cm'])->assertSessionHasErrors('matricule');
        $connecter($premier)->assertRedirect(route('bibliotheque.emprunts'));
        $this->post(route('bibliotheque.demander', $document))->assertRedirect(route('bibliotheque.emprunts'));
        $this->mailEnvoye($premier, EvenementEmprunt::Demande);
        Mail::assertSent(NouvelleDemandeEmprunt::class);
        $demande = Emprunt::firstOrFail();

        $this->actingAs($this->bibliothecaire());
        $this->patch(route('admin.emprunts.traiter', [$demande, 'valider']))->assertSessionHas('succes');
        $this->assertSame(StatutEmprunt::Reserve, $demande->fresh()->statut);
        $this->mailEnvoye($premier, EvenementEmprunt::Reserve);
        $this->patch(route('admin.emprunts.traiter', [$demande, 'remettre']))->assertSessionHas('succes');
        $this->assertSame(StatutEmprunt::EnCours, $demande->fresh()->statut);

        // Le second adhérent rejoint la file d'attente ; au retour, l'exemplaire lui est réservé
        $connecter($second);
        $this->post(route('bibliotheque.demander', $document));
        $attente = Emprunt::where('adherent_id', $second->id)->firstOrFail();
        $this->post(route('bibliotheque.annuler', $demande))->assertNotFound();

        $this->patch(route('admin.emprunts.retour', $demande), ['etat_physique' => 'bon']);
        $exemplaire = $demande->fresh()->exemplaire;
        $this->assertSame(StatutEmprunt::Reserve, $attente->fresh()->statut);
        $this->assertSame($exemplaire->id, $attente->fresh()->exemplaire_id);
        $this->assertSame(StatutExemplaire::Reserve, $exemplaire->statut);
        $this->mailEnvoye($second, EvenementEmprunt::Reserve);

        // Prêt au guichet : la réservation du second adhérent est servie
        $this->preter($second, $exemplaire)->assertSessionHas('succes');
        $this->assertSame(StatutEmprunt::EnCours, $attente->fresh()->statut);
        $this->assertSame(2, Emprunt::count());

        // Chaque modèle d'e-mail se génère
        foreach (EvenementEmprunt::cases() as $evenement) {
            $this->assertStringContainsString($document->titre, (new EmpruntNotification($attente->fresh(), $evenement))->render());
        }
        $this->assertStringContainsString($second->matricule, (new NouvelleDemandeEmprunt($attente->fresh()))->render());
    }

    public function test_la_tache_quotidienne(): void
    {
        $adherent = Adherent::factory()->create();
        $echu = Adherent::factory()->create(['date_expiration' => today()->subDay()]);
        $documents = Document::factory()->avecExemplaires(1)->count(3)->create();
        [$reservation, $bientot, $retard] = $documents->map(fn (Document $d) => Emprunt::create([
            'adherent_id' => $adherent->id, 'document_id' => $d->id, 'exemplaire_id' => $d->exemplaires->first()->id,
            'statut' => StatutEmprunt::EnCours, 'date_pret' => now()->subDays(10),
        ]));
        $reservation->update(['statut' => StatutEmprunt::Reserve, 'retirer_avant' => today()->subDay()]);
        $reservation->exemplaire->update(['statut' => StatutExemplaire::Reserve]);
        $bientot->update(['date_retour_prevue' => today()->addDay()]);
        $retard->update(['date_retour_prevue' => today()->subDays(4)]);

        $this->artisan('bibliotheque:echeances')->assertSuccessful();

        $this->assertSame(StatutAdherent::Expire, $echu->fresh()->statut);
        $this->assertSame(StatutEmprunt::Annule, $reservation->fresh()->statut);
        $this->assertSame(StatutExemplaire::Disponible, $reservation->exemplaire->fresh()->statut);
        $this->assertNotNull($bientot->fresh()->rappel_envoye_le);
        $this->assertNotNull($retard->fresh()->derniere_relance_le);
        foreach ([EvenementEmprunt::Expire, EvenementEmprunt::Rappel, EvenementEmprunt::Retard] as $evenement) {
            $this->mailEnvoye($adherent, $evenement);
        }

        Mail::fake();
        $this->artisan('bibliotheque:echeances');
        Mail::assertNothingSent();
    }
}
