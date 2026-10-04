@extends('layouts.app')
@section('titre', 'Technologies appropriées')

@section('contenu')
@include('partials.entete', [
    'titre' => 'Les technologies appropriées',
    'intro' => 'Des techniques conçues pour l\'environnement, la culture, l\'économie et les ressources de la communauté qui les utilise.',
    'image' => 'images/galerie/eolienne.webp',
    'ariane' => ['Technologies appropriées' => null],
])

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7 contenu">
                <p class="lead texte">Énergie solaire, éolienne, petite hydroélectricité, biométhanisation, biocarburants, recyclage, compostage, agriculture biologique.</p>
                <p class="texte">La technologie appropriée (TA, en anglais <span lang="en">Appropriate Technology</span>) accorde une attention particulière aux aspects environnementaux, éthiques, culturels, sociaux et économiques de la communauté à laquelle elle est destinée.</p>
                <p class="texte">Le mouvement a été porté par E. F. Schumacher, auteur de <cite>Small is Beautiful</cite> (1973) : les pays les moins développés ont besoin de technologies mieux adaptées à leurs ressources, moins coûteuses, et qui favorisent l'appropriation, la compréhension et l'autonomie.</p>
                <h2>Exemples</h2>
                <ul>
                    <li>Bélier hydraulique, vis d'Archimède, shadouf</li>
                    <li>Pompes à eau et filtres pour l'eau potable</li>
                    <li>Toilettes et latrines améliorées</li>
                    <li>Cuisinières solaires et foyers améliorés</li>
                    <li>Froid solaire et séchoirs mixtes</li>
                    <li>Biogaz et presses à huile</li>
                </ul>
                <h2>La technologie intermédiaire</h2>
                <p class="texte">Elle se situe entre les techniques traditionnelles, dont les performances limitent le développement, et la haute technologie, dont le coût et la formation qu'elle exige sont aussi un frein. Le concept apparaît dans les années 1960, avec l'<span lang="en">Intermediate Technology Development Group</span> fondé par Schumacher en 1966.</p>
                <p class="texte">L'idée : améliorer les pratiques en apportant des solutions peu coûteuses, faciles à mettre en œuvre avec des matériaux locaux, dans l'agriculture, l'alimentation, l'habitat, l'eau et la santé.</p>
                <a href="{{ asset('documents/technologies-appropriees.pdf') }}" class="btn btn-outline-primary mt-2" target="_blank" rel="noopener"><i class="bi bi-file-earmark-pdf me-1"></i> Télécharger le dossier (PDF)</a>
            </div>
            <div class="col-lg-5">
                <div class="row g-3">
                    <div class="col-12"><div class="photo ratio-large"><img src="{{ asset('images/galerie/filtre-biosable.webp') }}" alt="Filtre biosable pour l'eau potable" loading="lazy"></div></div>
                    <div class="col-6"><div class="photo ratio-photo"><img src="{{ asset('images/galerie/pompe.webp') }}" alt="Pompe à eau fabriquée au centre" loading="lazy"></div></div>
                    <div class="col-6"><div class="photo ratio-photo"><img src="{{ asset('images/galerie/charbon.webp') }}" alt="Charbon écologique" loading="lazy"></div></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section bg-brume">
    <div class="container">
        <h2 class="mb-4">Réalisations du centre</h2>
        @include('partials.galerie', ['photos' => $galerie])
    </div>
</section>
@endsection
