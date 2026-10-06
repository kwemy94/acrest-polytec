@extends('layouts.app')
@section('titre', 'Qui sommes-nous')

@section('contenu')
@include('partials.entete', [
    'titre' => 'L\'ACREST, centre africain des technologies appropriées',
    'intro' => 'Association camerounaise à but non lucratif créée en 2003 et enregistrée en 2005, à l\'origine d\'ACREST Polytechnique.',
    'image' => 'images/site/equipe-2018.webp',
    'ariane' => ['L\'ACREST' => null],
])

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7 contenu">
                <h2>Qui sommes-nous</h2>
                <p class="texte">Le Centre africain des technologies appropriées et des énergies nouvelles et renouvelables, connu sous le sigle anglais ACREST, a son siège à Tchuelekouet (« Champ des collines »), quartier du village Bangang dans le département des Bamboutos. Il se situe à 15 km de la ville universitaire de Dschang, sur l'axe Dschang – Mbouda.</p>

                <h2>Notre mission</h2>
                <p class="texte">Vulgariser et promouvoir les technologies appropriées et les énergies nouvelles et renouvelables qui améliorent les conditions de vie des populations.</p>

                <h2>Notre vision</h2>
                <p class="texte">Contribuer à l'autosuffisance technologique, énergétique et économique des populations pour créer des richesses en zones rurales et urbaines.</p>

                <h2>Nos actions</h2>
                <ul>
                    <li>Information</li>
                    <li>Démonstration</li>
                    <li>Formation</li>
                    <li>Fabrication de produits de première nécessité</li>
                    <li>Fourniture de services énergétiques et technologiques aux populations</li>
                </ul>

                <h2>Nos programmes</h2>
                <div class="row g-4">
                    <div class="col-md-6">
                        <h3>Énergies renouvelables</h3>
                        <ul>
                            <li>Énergie solaire thermique et photovoltaïque</li>
                            <li>Énergie hydraulique</li>
                            <li>Énergie éolienne</li>
                            <li>Biomasse (bois énergie)</li>
                            <li>Biogaz</li>
                            <li>Biodiesel</li>
                            <li>Énergie musculaire, humaine et animale</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h3>Technologies appropriées</h3>
                        <ul>
                            <li>Approvisionnement en eau potable</li>
                            <li>Transformation et conservation des aliments</li>
                            <li>Transport</li>
                            <li>Foyers améliorés</li>
                            <li>Architecture et matériaux locaux</li>
                        </ul>
                    </div>
                </div>

                <h2>Acquis et réalisations</h2>
                <p class="texte">ACREST se veut un lieu d'information, de démonstration et d'utilisation des ressources locales pour répondre aux besoins locaux, comme la construction de pompes manuelles simples qui facilitent l'accès à l'eau potable.</p>
                <p class="texte">La roue à aubes (noria) installée au centre utilise le courant de la rivière Mi pour élever l'eau vers un canal situé à 5 mètres de hauteur. Cette technologie, vieille de deux millénaires, fonctionne avec une énergie gratuite et inépuisable : un potentiel considérable pour l'irrigation et la sécurité alimentaire.</p>
                <p class="texte">L'énergie mécanique de la roue hydraulique permet aussi de moudre le maïs, ce qui évite aux habitants de longs déplacements vers les moulins électriques.</p>
                <p class="texte">ACREST s'inscrit dans la vision du NEPAD, qui mise sur le développement de l'Afrique à partir de ses propres ressources, et dans la lutte contre la pauvreté et pour le développement durable.</p>
            </div>
            <div class="col-lg-5">
                <div class="d-flex flex-column gap-4">
                    <figure class="m-0"><div class="photo ratio-photo"><img src="{{ asset('images/site/acrest-roue.webp') }}" alt="Roue hydraulique de ACREST" loading="lazy"></div><figcaption>Roue hydraulique sur la rivière Mi</figcaption></figure>
                    <figure class="m-0"><div class="photo ratio-photo"><img src="{{ asset('images/site/acrest-pompe.webp') }}" alt="Pompe manuelle à eau" loading="lazy"></div><figcaption>Pompe manuelle pour l'eau potable</figcaption></figure>
                    <figure class="m-0"><div class="photo ratio-photo"><img src="{{ asset('images/site/acrest-noria.webp') }}" alt="Noria élevant l'eau vers un canal" loading="lazy"></div><figcaption>La noria élève l'eau à 5 m de hauteur</figcaption></figure>
                    <div class="encart">
                        <h2 class="h5">Pour aller plus loin</h2>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><a href="{{ asset('documents/presentation-acrest-polytechnique.pdf') }}" target="_blank" rel="noopener"><i class="bi bi-file-earmark-pdf me-1"></i> Présentation d'ACREST Polytechnique</a></li>
                            <li><a href="{{ asset('documents/programme-formation.pdf') }}" target="_blank" rel="noopener"><i class="bi bi-file-earmark-pdf me-1"></i> Programme de formation</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
