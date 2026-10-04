@extends('layouts.app')
@section('titre', 'Filières')

@section('contenu')
@include('partials.entete', [
    'titre' => 'Nos filières de formation',
    'intro' => 'Un brevet de technicien supérieur (BTS, bac + 2) associe cours théoriques, travaux pratiques en atelier et stages en entreprise.',
    'ariane' => ['Filières' => null],
])

<section class="section">
    <div class="container">
        @foreach ($domaines as $domaine => $filieres)
            <div class="mb-5">
                <h2 class="h3 mb-3">{{ $domaine }}</h2>
                <div class="row g-4">
                    @foreach ($filieres as $filiere)
                        <div class="col-md-6 col-xl-4">
                            <article class="h-100 d-flex flex-column">
                                <a href="{{ route('filieres.show', $filiere->slug) }}" class="photo ratio-large d-block mb-3">
                                    <img src="{{ asset($filiere->image) }}" alt="" loading="lazy">
                                </a>
                                <h3 class="h4 mb-2"><a href="{{ route('filieres.show', $filiere->slug) }}" class="text-decoration-none text-reset">{{ $filiere->nom }}</a></h3>
                                <p class="text-gris mb-2">{{ $filiere->description }}</p>
                                <p class="small mb-0 mt-auto">
                                    <strong>{{ $filiere->specialites_count }} spécialité{{ $filiere->specialites_count > 1 ? 's' : '' }} :</strong>
                                    {{ $filiere->specialites->pluck('nom')->join(', ', ' et ') }}
                                </p>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="encart d-flex flex-column flex-md-row gap-3 align-items-md-center justify-content-between">
            <div>
                <h2 class="h5 mb-1">Le programme complet</h2>
                <p class="mb-0 text-gris">Toutes les spécialités et leurs contenus dans un seul document.</p>
            </div>
            <a href="{{ asset('documents/specialites-et-programme.pdf') }}" class="btn btn-outline-primary" target="_blank" rel="noopener"><i class="bi bi-download me-1"></i> Télécharger (PDF)</a>
        </div>
    </div>
</section>
@endsection
