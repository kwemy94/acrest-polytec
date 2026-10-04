@extends('layouts.admin')
@section('titre', 'Mon compte')

@section('contenu')
<div class="admin-carte p-3 p-lg-4" style="max-width: 520px">
    <h2 class="h5 mb-1">{{ auth()->user()->name }}</h2>
    <p class="text-gris">{{ auth()->user()->email }}</p>
    <h3 class="h6 mt-4 mb-3">Changer de mot de passe</h3>
    <form method="post" action="{{ route('admin.compte.update') }}">
        @csrf @method('PUT')
        <div class="mb-3">
            <label for="actuel" class="form-label">Mot de passe actuel</label>
            <input type="password" id="actuel" name="actuel" class="form-control @error('actuel') is-invalid @enderror" required autocomplete="current-password">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Nouveau mot de passe</label>
            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password" aria-describedby="pwd-aide">
            <div id="pwd-aide" class="form-text">8 caractères minimum, avec des lettres et des chiffres.</div>
        </div>
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Confirmer le nouveau mot de passe</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required autocomplete="new-password">
        </div>
        <button class="btn btn-primary">Enregistrer le mot de passe</button>
    </form>
</div>
@endsection
