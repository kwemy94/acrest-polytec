@extends('layouts.app')
@section('titre', 'Spécialités')

@section('contenu')
@include('partials.entete', [
    'titre' => 'Toutes les spécialités',
    'intro' => 'Choisissez votre futur métier parmi les spécialités de BTS proposées par ACREST Polytechnique.',
    'image' => 'images/galerie/stator-15kw.webp',
    'ariane' => ['Spécialités' => null],
])

<section class="section">
    <div class="container">
        <nav class="d-flex flex-wrap gap-2 mb-5" aria-label="Aller à une filière">
            @foreach ($filieres as $filiere)
                <a href="#{{ $filiere->slug }}" class="btn btn-sm btn-outline-primary">{{ $filiere->nom }}</a>
            @endforeach
        </nav>

        @foreach ($filieres as $filiere)
            <section id="{{ $filiere->slug }}" class="mb-5" style="scroll-margin-top: 100px">
                <div class="d-flex justify-content-between align-items-baseline gap-3 mb-3 border-bottom pb-2">
                    <h2 class="h3 mb-0">{{ $filiere->nom }}</h2>
                    <a href="{{ route('filieres.show', $filiere->slug) }}" class="small text-nowrap">La filière</a>
                </div>
                <div class="row g-3">
                    @foreach ($filiere->specialites as $specialite)
                        <div class="col-md-6 col-lg-4">
                            @include('partials.ligne-specialite', ['specialite' => $specialite])
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
</section>
@endsection
