@extends('layouts.app')
@section('titre', 'Page introuvable')

@section('contenu')
<section class="section">
    <div class="container text-center" style="max-width: 560px">
        <p class="display-titre" style="font-size:5rem;color:var(--c-soleil)">404</p>
        <h1 class="h2">Cette page n'existe pas ou a été déplacée</h1>
        <p class="text-gris">Vérifiez l'adresse ou repartez de l'accueil.</p>
        <div class="d-flex gap-2 justify-content-center flex-wrap">
            <a href="{{ route('accueil') }}" class="btn btn-primary">Retour à l'accueil</a>
            <a href="{{ route('specialites.index') }}" class="btn btn-outline-primary">Voir les spécialités</a>
        </div>
    </div>
</section>
@endsection
