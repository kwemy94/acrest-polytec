@extends('layouts.app')
@section('titre', 'Espace adhérent')

@section('contenu')
<div class="inscription-fond pt-5 pb-5">
    <div class="container" style="max-width: 560px">
        <div class="carte-form">
            <div class="carte-form-corps">
                <header class="etape-entete">
                    <i class="bi bi-book" aria-hidden="true"></i>
                    <div>
                        <h1>Espace adhérent</h1>
                        <p>Suivez vos prêts, faites une demande et accédez aux documents numériques.</p>
                    </div>
                </header>
                @include('partials.flash')
                <form method="post" action="{{ route('bibliotheque.connexion.store') }}" class="needs-validation" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="matricule" class="form-label">Matricule</label>
                        <input type="text" id="matricule" name="matricule" value="{{ old('matricule') }}" class="form-control text-uppercase @error('matricule') is-invalid @enderror" required autofocus autocomplete="off">
                        <div class="invalid-feedback">@error('matricule'){{ $message }}@else Saisissez votre matricule. @enderror</div>
                        <div class="form-text">Étudiants : votre code d'inscription (ISAP-…).</div>
                    </div>
                    <div class="mb-4">
                        <label for="email" class="form-label">Adresse e-mail enregistrée à la bibliothèque</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autocomplete="email">
                        <div class="invalid-feedback">@error('email'){{ $message }}@else Saisissez votre adresse e-mail. @enderror</div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100" data-chargement="Connexion…">Accéder à mon espace</button>
                </form>
                <hr class="my-4">
                <div class="small">
                    <a href="{{ route('bibliotheque.index') }}"><i class="bi bi-arrow-left me-1"></i>Retour au catalogue</a>
                    <p class="text-gris mt-2 mb-0">Pas encore adhérent ? Présentez-vous à la bibliothèque, {{ config('acrest.bibliotheque.horaires') }}.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
