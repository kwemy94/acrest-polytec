@extends('layouts.admin')
@section('titre', 'Historique des opérations')

@section('contenu')
<form method="get" class="admin-carte p-3 mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-lg-3">
            <label for="q" class="form-label small">Recherche</label>
            <input type="search" id="q" name="q" value="{{ $filtres['q'] ?? '' }}" class="form-control" placeholder="Code, titre, nom…">
        </div>
        <div class="col-6 col-lg-3">
            <label for="action" class="form-label small">Opération</label>
            <select id="action" name="action" class="form-select">
                <option value="">Toutes</option>
                @foreach ($actions as $cle => $libelle)<option value="{{ $cle }}" @selected(($filtres['action'] ?? '') === $cle)>{{ $libelle }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-lg-2">
            <label for="user" class="form-label small">Utilisateur</label>
            <select id="user" name="user" class="form-select">
                <option value="">Tous</option>
                @foreach ($utilisateurs as $u)<option value="{{ $u->id }}" @selected((string) ($filtres['user'] ?? '') === (string) $u->id)>{{ $u->name }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-lg-2">
            <label for="du" class="form-label small">Du</label>
            <input type="date" id="du" name="du" value="{{ $filtres['du'] ?? '' }}" class="form-control">
        </div>
        <div class="col-6 col-lg-1">
            <label for="au" class="form-label small">Au</label>
            <input type="date" id="au" name="au" value="{{ $filtres['au'] ?? '' }}" class="form-control">
        </div>
        <div class="col-lg-1"><button class="btn btn-outline-primary w-100" title="Filtrer"><i class="bi bi-funnel"></i></button></div>
    </div>
</form>

<div class="admin-carte">
    @include('admin.bibliotheque._journal', ['activites' => $activites])
    @if ($activites->hasPages())<div class="p-3">{{ $activites->links() }}</div>@endif
</div>
@endsection
