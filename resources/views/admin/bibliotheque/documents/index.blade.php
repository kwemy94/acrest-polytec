@extends('layouts.admin')
@section('titre', 'Documents')

@section('contenu')
<form method="get" class="admin-carte p-3 mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-lg-4">
            <label for="q" class="form-label small">Recherche</label>
            <input type="search" id="q" name="q" value="{{ $filtres['q'] ?? '' }}" class="form-control" placeholder="Titre, auteur, ISBN/ISSN, mot-clé, code d'inventaire…">
        </div>
        <div class="col-6 col-lg-2">
            <label for="type" class="form-label small">Type</label>
            <select id="type" name="type" class="form-select">
                <option value="">Tous</option>
                @foreach ($types as $t)<option value="{{ $t->id }}" @selected((string) ($filtres['type'] ?? '') === (string) $t->id)>{{ $t->nom }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-lg-2">
            <label for="categorie" class="form-label small">Catégorie</label>
            <select id="categorie" name="categorie" class="form-select">
                <option value="">Toutes</option>
                @foreach ($categories as $c)<option value="{{ $c->id }}" @selected((string) ($filtres['categorie'] ?? '') === (string) $c->id)>{{ $c->nom }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-lg-2">
            <label for="localisation" class="form-label small">Localisation</label>
            <select id="localisation" name="localisation" class="form-select">
                <option value="">Toutes</option>
                @foreach ($localisations as $id => $libelle)<option value="{{ $id }}" @selected((string) ($filtres['localisation'] ?? '') === (string) $id)>{{ $libelle }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-lg-2 d-flex gap-2">
            <button class="btn btn-outline-primary flex-fill" title="Filtrer"><i class="bi bi-funnel"></i></button>
            <a href="{{ route('admin.documents.create') }}" class="btn btn-primary flex-fill" title="Nouveau document"><i class="bi bi-plus-lg"></i></a>
        </div>
        <div class="col-12 d-flex flex-wrap gap-4 small">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="disponible" name="disponible" value="1" @checked($filtres['disponible'] ?? false) onchange="this.form.submit()">
                <label class="form-check-label" for="disponible">Avec exemplaire disponible</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="numerique" name="numerique" value="1" @checked($filtres['numerique'] ?? false) onchange="this.form.submit()">
                <label class="form-check-label" for="numerique">Avec ressource numérique</label>
            </div>
        </div>
    </div>
</form>

<div class="admin-carte">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Titre</th><th>Type</th><th>Exemplaires</th><th>Disponibilité et localisation</th></tr></thead>
            <tbody>
                @forelse ($documents as $d)
                    <tr>
                        <td>
                            <a href="{{ route('admin.documents.show', $d) }}" class="fw-semibold text-decoration-none">{{ $d->titre_complet }}</a>
                            <div class="small text-gris">{{ $d->noms_auteurs ?: 'Auteur non renseigné' }}{{ $d->annee_publication ? ' · '.$d->annee_publication : '' }} · {{ $d->langue_libelle }}</div>
                        </td>
                        <td class="small">{{ $d->type->nom }}<div class="text-gris">{{ $d->categorie?->nom }}</div></td>
                        <td class="small text-nowrap">{{ $d->exemplaires_count }} · {{ $d->empruntes_count }} emprunté{{ $d->empruntes_count > 1 ? 's' : '' }}</td>
                        <td>
                            <div class="d-flex flex-wrap gap-1 mb-1">@include('bibliotheque._disponibilite', ['document' => $d])</div>
                            @include('bibliotheque._localisations', ['document' => $d])
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-4 text-gris">Aucun document. <a href="{{ route('admin.documents.create') }}">Ajoutez le premier</a>.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($documents->hasPages())<div class="p-3">{{ $documents->links() }}</div>@endif
</div>
@endsection
