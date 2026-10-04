@extends('layouts.app')
@section('titre', 'Espace bibliothèque')

@section('contenu')
<div class="inscription-fond pt-5 pb-5">
    <div class="container" style="max-width: 560px">
        <div class="carte-form">
            <div class="carte-form-corps">
                <header class="etape-entete">
                    <i class="bi bi-book" aria-hidden="true"></i>
                    <div>
                        <h1>Espace bibliothèque</h1>
                        <p>Réservé aux étudiants dont le dossier d'inscription est validé.</p>
                    </div>
                </header>
                @include('partials.flash')
                <form method="post" action="{{ route('bibliotheque.connexion.store') }}" class="needs-validation" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="code" class="form-label">Code d'inscription</label>
                        <input type="text" id="code" name="code" value="{{ old('code') }}" class="form-control text-uppercase @error('code') is-invalid @enderror" placeholder="ISAP-26-XXXXXX" required autofocus autocomplete="off">
                        <div class="invalid-feedback">@error('code'){{ $message }}@else Saisissez votre code d'inscription. @enderror</div>
                    </div>
                    <div class="mb-4">
                        <label for="email" class="form-label">Adresse e-mail utilisée lors de l'inscription</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autocomplete="email">
                        <div class="invalid-feedback">@error('email'){{ $message }}@else Saisissez votre adresse e-mail. @enderror</div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100" data-chargement="Connexion…">Accéder à mes emprunts</button>
                </form>
                <hr class="my-4">
                <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 small">
                    <a href="{{ route('bibliotheque.index') }}"><i class="bi bi-arrow-left me-1"></i>Retour au catalogue</a>
                    <a href="{{ route('dossier.retrouver') }}"><i class="bi bi-key me-1"></i>J'ai perdu mon code</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
