@extends('layouts.admin')
@section('titre', 'Exemplaires')

@section('contenu')
<form method="get" class="admin-carte p-3 mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-lg-4">
            <label for="q" class="form-label small">Recherche</label>
            <input type="search" id="q" name="q" value="{{ $filtres['q'] ?? '' }}" class="form-control" placeholder="Code d'inventaire, code-barres, titre…" autofocus>
        </div>
        <div class="col-6 col-lg-2">
            <label for="statut" class="form-label small">Statut</label>
            <select id="statut" name="statut" class="form-select">
                <option value="">Tous</option>
                @foreach ($statuts as $s)<option value="{{ $s->value }}" @selected(($filtres['statut'] ?? '') === $s->value)>{{ $s->libelle() }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-lg-2">
            <label for="etat" class="form-label small">État</label>
            <select id="etat" name="etat" class="form-select">
                <option value="">Tous</option>
                @foreach ($etats as $e)<option value="{{ $e->value }}" @selected(($filtres['etat'] ?? '') === $e->value)>{{ $e->libelle() }}</option>@endforeach
            </select>
        </div>
        <div class="col-8 col-lg-3">
            <label for="localisation" class="form-label small">Localisation</label>
            <select id="localisation" name="localisation" class="form-select">
                <option value="">Toutes</option>
                <option value="aucune" @selected(($filtres['localisation'] ?? '') === 'aucune')>Sans localisation</option>
                @foreach ($localisations as $id => $libelle)<option value="{{ $id }}" @selected((string) ($filtres['localisation'] ?? '') === (string) $id)>{{ $libelle }}</option>@endforeach
            </select>
        </div>
        <div class="col-4 col-lg-1"><button class="btn btn-outline-primary w-100" title="Filtrer"><i class="bi bi-funnel"></i></button></div>
    </div>
</form>

<div class="admin-carte">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Inventaire</th><th>Document</th><th>Localisation</th><th>État</th><th>Statut</th></tr></thead>
            <tbody>
                @forelse ($exemplaires as $ex)
                    <tr>
                        <td><a href="{{ route('admin.exemplaires.show', $ex) }}" class="fw-semibold"><code>{{ $ex->code_inventaire }}</code></a></td>
                        <td><a href="{{ route('admin.documents.show', $ex->document) }}" class="text-decoration-none">{{ $ex->document->titre }}</a><div class="small text-gris">{{ $ex->document->noms_auteurs }}</div></td>
                        <td class="small">{{ $ex->localisation?->chemin ?? '—' }}</td>
                        <td><x-statut :statut="$ex->etat_physique" /></td>
                        <td>
                            <x-statut :statut="$ex->statut" />
                            @if ($ex->empruntActif)<div class="small text-gris mt-1">{{ $ex->empruntActif->adherent->nom_complet }}</div>@endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-4 text-gris">Aucun exemplaire ne correspond à ces critères.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($exemplaires->hasPages())<div class="p-3">{{ $exemplaires->links() }}</div>@endif
</div>
@endsection
