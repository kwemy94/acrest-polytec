@extends('layouts.app')
@section('titre', 'Logement')

@section('contenu')
@include('partials.entete', [
    'titre' => 'Se loger sur le campus',
    'intro' => 'Des chambres individuelles en résidence, pour l\'année ou pour une période plus courte.',
    'image' => 'images/site/logement-2.webp',
    'ariane' => ['Logement' => null],
])

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7 contenu">
                <p class="texte">Plusieurs formules de location sont proposées dans les résidences, temporaires ou pour toute l'année académique, selon le public : étudiants de l'ISAP, enseignants, stagiaires. Ce mode de logement favorise la vie commune entre étudiants de tous pays et de tous niveaux.</p>
                <div class="alert alert-warning">
                    <strong>Les places sont limitées.</strong> Votre admission ne garantit pas une chambre : faites votre demande le plus tôt possible.
                </div>

                <h2>Comment obtenir une chambre</h2>
                <ol class="ps-3">
                    <li class="mb-2">Retirez la fiche de renseignements auprès du service des ressources humaines (SRH) de l'ISAP.</li>
                    <li class="mb-2">Déposez la fiche dûment remplie au SRH.</li>
                    <li class="mb-2">La commission d'attribution étudie les dossiers et publie la liste des résidents retenus.</li>
                </ol>

                <h2>Critères de sélection</h2>
                <ul>
                    <li>L'éloignement du domicile des parents</li>
                    <li>Les résultats académiques</li>
                    <li>Une priorité est accordée aux étudiants étrangers</li>
                </ul>

                <p class="mt-4">Pour toute question : <a href="mailto:{{ config('acrest.contact.email') }}">{{ config('acrest.contact.email') }}</a> ou <a href="tel:{{ preg_replace('/\s+/', '', config('acrest.contact.telephone')) }}">{{ config('acrest.contact.telephone') }}</a>.</p>
            </div>
            <div class="col-lg-5 d-flex flex-column gap-4">
                <figure class="m-0"><div class="photo ratio-photo"><img src="{{ asset('images/site/logement-1.webp') }}" alt="Résidence étudiante" loading="lazy"></div><figcaption>Dortoirs</figcaption></figure>
                <figure class="m-0"><div class="photo ratio-photo"><img src="{{ asset('images/site/logement-2.webp') }}" alt="Ateliers, salles de cours et logements" loading="lazy"></div><figcaption>Ateliers, salles de cours et logements</figcaption></figure>
            </div>
        </div>
    </div>
</section>
@endsection
