@extends('layouts.app')

@section('description', 'ACREST Polytechnique forme en deux ans (BTS) aux métiers des énergies renouvelables, de l\'eau, du génie civil, de l\'agriculture, de la gestion et de la santé, à Bangang (Ouest-Cameroun).')

@section('contenu')
<section class="hero" style="--hero-img: url('{{ asset('images/site/hero-noria.webp') }}'); --hero-img-mobile: url('{{ asset('images/site/hero-noria-mobile.webp') }}')">
    <div class="container pb-5 pt-5">
        <h1 class="mb-3">Apprendre les métiers de l'énergie au pied des Bamboutos.</h1>
        <p class="mb-4">Brevet de technicien supérieur en {{ $filieres->count() }} filières et {{ $nombreSpecialites }} spécialités, enseignées sur un site où l'on construit soi-même roues hydrauliques, éoliennes et installations solaires.</p>
        <div class="d-flex flex-wrap gap-2 mb-5">
            <a href="{{ route('inscription.debut') }}" class="btn btn-soleil btn-lg">Commencer mon inscription</a>
            <a href="{{ route('filieres.index') }}" class="btn btn-outline-light btn-lg">Découvrir les formations</a>
        </div>
        <p class="hero-legende mb-0"><i class="bi bi-camera me-1"></i> Roue à aubes de 5 m construite par l'ACREST sur la rivière Mi, à Bangang.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <h2 class="mb-3">Former aux métiers dont le Cameroun a besoin</h2>
                <p class="texte">ACREST Polytechnique forme les jeunes aux métiers du XXI<sup>e</sup> siècle : accès à l'énergie et à l'eau, sécurité alimentaire, emplois verts, lutte contre les changements climatiques et maîtrise des technologies de l'information.</p>
                <p class="texte">La formation s'appuie sur le savoir-faire de l'ACREST, association créée en 2003 qui conçoit et fabrique sur place des technologies appropriées au contexte local.</p>
                <div class="row g-4 mt-2">
                    <div class="col-sm-6"><div class="repere"><strong>40 %</strong> des Camerounais ont accès à l'électricité (Banque mondiale, 2010).</div></div>
                    <div class="col-sm-6"><div class="repere"><strong>35 GW</strong> de potentiel hydroélectrique, pour environ 1 GW installé.</div></div>
                </div>
                <a href="{{ route('acrest') }}" class="btn btn-outline-primary mt-4">Qui sommes-nous</a>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-7"><div class="photo" style="aspect-ratio:3/4"><img src="{{ asset('images/site/solaire-25kw.webp') }}" alt="Installation solaire photovoltaïque de 25 kW au centre ACREST" loading="lazy"></div></div>
                    <div class="col-5 d-flex flex-column gap-3">
                        <div class="photo flex-fill"><img src="{{ asset('images/site/atelier-2018.webp') }}" alt="Étudiants en atelier de fabrication" loading="lazy"></div>
                        <div class="photo flex-fill"><img src="{{ asset('images/galerie/lampes-led.webp') }}" alt="Atelier de montage de lampes LED" loading="lazy"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section bg-brume">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div>
                <h2 class="mb-2">Nos filières</h2>
                <p class="text-gris mb-0 texte-court">Chaque filière regroupe des spécialités de BTS, préparées en deux ans avec cours, ateliers et stages.</p>
            </div>
            <a href="{{ route('specialites.index') }}" class="btn btn-outline-primary">Voir les {{ $nombreSpecialites }} spécialités</a>
        </div>
        <div class="mosaique">
            @foreach ($filieres as $filiere)
                <a class="tuile" href="{{ route('filieres.show', $filiere->slug) }}">
                    <img src="{{ asset($filiere->image) }}" alt="" loading="lazy">
                    <span class="tuile-texte">
                        <strong>{{ $filiere->nom }}</strong>
                        <span>{{ $filiere->specialites_count }} spécialité{{ $filiere->specialites_count > 1 ? 's' : '' }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="section bg-foret">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <h2 class="text-white mb-3">S'inscrire en ligne</h2>
                <p class="text-white-50 mb-4">Comptez une dizaine de minutes. Gardez votre pièce d'identité et votre diplôme à portée de main.</p>
                <a href="{{ route('inscription.debut') }}" class="btn btn-soleil">Commencer mon inscription</a>
            </div>
            <div class="col-lg-8">
                <ol class="parcours">
                    <li>
                        <h3>Remplir le formulaire</h3>
                        <p>Identité, coordonnées, diplôme puis jusqu'à trois spécialités par ordre de préférence.</p>
                    </li>
                    <li>
                        <h3>Payer les frais</h3>
                        <p>{{ number_format(config('acrest.paiement.frais_inscription'), 0, ',', ' ') }} FCFA par MTN Mobile Money ou Orange Money, puis déclarez la référence reçue par SMS.</p>
                    </li>
                    <li>
                        <h3>Suivre son dossier</h3>
                        <p>Avec votre code d'inscription, consultez à tout moment l'état de votre dossier et de votre paiement.</p>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div>
                <h2 class="mb-2">Réalisé au centre</h2>
                <p class="text-gris mb-0 texte-court">Des équipements conçus, fabriqués et entretenus par les équipes et les étudiants de l'ACREST.</p>
            </div>
            <a href="{{ route('technologies') }}" class="btn btn-outline-primary">Les technologies appropriées</a>
        </div>
        @include('partials.galerie', ['photos' => $galerie])
    </div>
</section>
@endsection
