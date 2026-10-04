@extends('layouts.app')
@section('titre', $filiere->nom)
@section('description', $filiere->description)

@section('contenu')
@include('partials.entete', [
    'titre' => $filiere->nom,
    'intro' => $filiere->description,
    'image' => $filiere->image,
    'ariane' => ['Filières' => route('filieres.index'), $filiere->nom => null],
])

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <h2 class="h3 mb-1">Spécialités de la filière</h2>
                <p class="text-gris mb-4">Domaine : {{ $filiere->domaine }}</p>
                <div class="d-flex flex-column gap-3">
                    @foreach ($filiere->specialites as $specialite)
                        @include('partials.ligne-specialite', ['specialite' => $specialite, 'sousTitre' => Str::limit($specialite->resume, 110)])
                    @endforeach
                </div>
            </div>
            <div class="col-lg-4">
                <div class="encart encart-foret sticky-encart">
                    <h2 class="h4">Intéressé par cette filière ?</h2>
                    <p class="text-white-50">Vous pourrez choisir jusqu'à trois spécialités, dans une ou plusieurs filières.</p>
                    <a href="{{ route('inscription.debut') }}" class="btn btn-soleil w-100">Commencer mon inscription</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
