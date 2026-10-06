@extends('layouts.admin')
@section('titre', 'Localisations')

@section('contenu')
<div class="row g-3">
    <div class="col-xl-8">
        <div class="admin-carte">
            <div class="p-3 p-lg-4 pb-0">
                <h2 class="h5 mb-1">Plan de la bibliothèque</h2>
                <p class="small text-gris">Chaque espace peut contenir d'autres espaces : bibliothèque → salle → rayon → étagère → niveau.</p>
            </div>
            <ul class="list-unstyled mb-0">
                @forelse ($arbre as ['localisation' => $loc, 'profondeur' => $p])
                    <li class="d-flex align-items-center gap-2 border-top py-2 pe-3" style="padding-left: {{ 1 + $p * 1.5 }}rem">
                        <i class="bi {{ $loc->type->icone() }} text-foret"></i>
                        <a href="{{ route('admin.localisations.show', $loc) }}" class="fw-semibold text-decoration-none">{{ $loc->nom }}</a>
                        <span class="small text-gris">{{ $loc->type->libelle() }}{{ $loc->code ? ' · '.$loc->code : '' }}</span>
                        <span class="ms-auto small text-gris">{{ $nombres[$loc->id] ?? 0 }} ex.</span>
                    </li>
                @empty
                    <li class="p-4 text-gris border-top">Aucun espace. Commencez par créer la bibliothèque principale.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-xl-4">
        <form method="post" action="{{ route('admin.localisations.store') }}" class="admin-carte p-3 p-lg-4">
            @csrf
            <h2 class="h6 mb-3"><i class="bi bi-plus-lg me-1"></i>Nouvel espace</h2>
            <label for="parent_id" class="form-label small">Dans</label>
            <select id="parent_id" name="parent_id" class="form-select form-select-sm mb-2">
                <option value="">— Niveau principal —</option>
                @foreach ($options as $id => $l)<option value="{{ $id }}" @selected((string) old('parent_id') === (string) $id)>{{ $l }}</option>@endforeach
            </select>
            <label for="nom" class="form-label small">Nom</label>
            <input id="nom" name="nom" value="{{ old('nom') }}" class="form-control form-control-sm mb-2 @error('nom') is-invalid @enderror" required maxlength="100" placeholder="Ex. : Salle Sciences, Étagère A">
            <div class="row g-2 mb-2">
                <div class="col-7">
                    <label for="type" class="form-label small">Type</label>
                    <select id="type" name="type" class="form-select form-select-sm">@foreach ($types as $t)<option value="{{ $t->value }}" @selected(old('type') === $t->value)>{{ $t->libelle() }}</option>@endforeach</select>
                </div>
                <div class="col-5">
                    <label for="code" class="form-label small">Code</label>
                    <input id="code" name="code" value="{{ old('code') }}" class="form-control form-control-sm" maxlength="30">
                </div>
            </div>
            <button class="btn btn-sm btn-primary">Créer</button>
        </form>
    </div>
</div>
@endsection
