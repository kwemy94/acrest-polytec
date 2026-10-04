@extends('layouts.app')
@section('titre', 'Code oublié')

@section('contenu')
<div class="inscription-fond pt-5 pb-5">
    <div class="container" style="max-width: 560px">
        <div class="carte-form">
            <div class="carte-form-corps">
                <header class="etape-entete">
                    <i class="bi bi-key" aria-hidden="true"></i>
                    <div>
                        <h1>Retrouver mon code</h1>
                        <p>Nous vous renvoyons votre code d'inscription par e-mail.</p>
                    </div>
                </header>
                @include('partials.flash')
                <form method="post" action="{{ route('dossier.envoyer-code') }}" class="needs-validation" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse e-mail</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus autocomplete="email">
                        <div class="invalid-feedback">@error('email'){{ $message }}@else Saisissez votre adresse e-mail. @enderror</div>
                    </div>
                    <div class="mb-4">
                        <label for="cni" class="form-label">Numéro de CNI ou de passeport</label>
                        <input type="text" id="cni" name="cni" value="{{ old('cni') }}" class="form-control text-uppercase @error('cni') is-invalid @enderror" required>
                        <div class="invalid-feedback">@error('cni'){{ $message }}@else Saisissez le numéro de votre pièce d'identité. @enderror</div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100" data-chargement="Envoi…">Recevoir mon code</button>
                </form>
                <p class="small text-gris mt-4 mb-0">Plus accès à cette adresse ? Contactez la scolarité au {{ config('acrest.contact.telephone') }} avec votre pièce d'identité.</p>
            </div>
        </div>
    </div>
</div>
@endsection
