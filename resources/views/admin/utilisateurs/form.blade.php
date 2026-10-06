@extends('layouts.admin')
@section('titre', $utilisateur->exists ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur')

@section('contenu')
<a href="{{ route('admin.utilisateurs.index') }}" class="small d-inline-block mb-3"><i class="bi bi-arrow-left me-1"></i>Utilisateurs</a>

<form method="post" action="{{ $utilisateur->exists ? route('admin.utilisateurs.update', $utilisateur) : route('admin.utilisateurs.store') }}" class="admin-carte p-3 p-lg-4" style="max-width: 560px">
    @csrf
    @if ($utilisateur->exists) @method('PUT') @endif
    <div class="mb-3">
        <label for="name" class="form-label">Nom</label>
        <input id="name" name="name" value="{{ old('name', $utilisateur->name) }}" class="form-control @error('name') is-invalid @enderror" required maxlength="255">
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">E-mail (identifiant de connexion)</label>
        <input id="email" name="email" type="email" value="{{ old('email', $utilisateur->email) }}" class="form-control @error('email') is-invalid @enderror" required>
    </div>
    <div class="mb-3">
        <label for="role" class="form-label">Rôle</label>
        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror">
            @foreach (\App\Enums\Role::cases() as $r)<option value="{{ $r->value }}" @selected(old('role', $utilisateur->role?->value) === $r->value)>{{ $r->libelle() }}</option>@endforeach
        </select>
    </div>
    <div class="row g-3 mb-3">
        <div class="col-sm-6">
            <label for="password" class="form-label">Mot de passe @if ($utilisateur->exists)<span class="facultatif">(laisser vide pour conserver)</span>@endif</label>
            <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" @unless ($utilisateur->exists) required @endunless minlength="8">
        </div>
        <div class="col-sm-6">
            <label for="password_confirmation" class="form-label">Confirmation</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password">
        </div>
    </div>
    <div class="form-check mb-4">
        <input type="hidden" name="actif" value="0">
        <input class="form-check-input" type="checkbox" id="actif" name="actif" value="1" @checked(old('actif', $utilisateur->actif ?? true))>
        <label class="form-check-label" for="actif">Compte actif (peut se connecter)</label>
    </div>
    <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
</form>
@endsection
