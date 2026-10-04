@extends('layouts.admin')
@section('titre', 'Catalogue de la bibliothèque')

@section('contenu')
<div class="row g-3 mb-3">
    <div class="col-4"><div class="admin-carte p-3 h-100"><div class="small text-gris mb-1">Documents</div><div class="admin-chiffre">{{ $stats['documents'] }}</div></div></div>
    <div class="col-4"><div class="admin-carte p-3 h-100"><div class="small text-gris mb-1">Exemplaires en circulation</div><div class="admin-chiffre">{{ $stats['exemplaires'] }}</div></div></div>
    <div class="col-4"><div class="admin-carte p-3 h-100"><div class="small text-gris mb-1">En rayon</div><div class="admin-chiffre">{{ $stats['disponibles'] }}</div></div></div>
</div>

<form method="get" class="admin-carte p-3 mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-3">
            <label for="q" class="form-label small">Recherche</label>
            <input type="search" id="q" name="q" value="{{ $filtres['q'] ?? '' }}" class="form-control" placeholder="Titre, auteur, ISBN, cote, code-barres…">
        </div>
        <div class="col-6 col-md-2">
            <label for="langue" class="form-label small">Langue</label>
            <select id="langue" name="langue" class="form-select">
                <option value="">Toutes</option>
                @foreach (config('acrest.langues') as $code => $libelle)<option value="{{ $code }}" @selected(($filtres['langue'] ?? '') === $code)>{{ $libelle }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label for="type" class="form-label small">Type</label>
            <select id="type" name="type" class="form-select">
                <option value="">Tous</option>
                @foreach ($types as $t)<option value="{{ $t->value }}" @selected(($filtres['type'] ?? '') === $t->value)>{{ $t->libelle() }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-md-1"><button class="btn btn-outline-primary w-100" title="Filtrer"><i class="bi bi-funnel"></i></button></div>
        <div class="col-6 col-md-2"><a href="{{ route('admin.documents.create') }}" class="btn btn-primary w-100"><i class="bi bi-plus-lg"></i> Ajouter</a></div>
        <div class="col-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="numerique" name="numerique" value="1" @checked($filtres['numerique'] ?? false) onchange="this.form.submit()">
                <label class="form-check-label small" for="numerique">Uniquement les documents avec version PDF</label>
            </div>
        </div>
    </div>
</form>

<div class="admin-carte">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Titre</th><th>Type</th><th>Langue</th><th>Cote</th><th>Disponibilité</th></tr></thead>
            <tbody>
                @forelse ($documents as $d)
                    <tr>
                        <td>
                            <a href="{{ route('admin.documents.show', $d) }}" class="fw-semibold text-decoration-none">{{ $d->titre }}</a>
                            <div class="small text-gris">{{ $d->auteurs }}{{ $d->annee_publication ? ' · '.$d->annee_publication : '' }}</div>
                        </td>
                        <td class="small">{{ $d->type->libelle() }}</td>
                        <td class="small">{{ $d->langue_libelle }}</td>
                        <td class="small">{{ $d->cote ?: '—' }}</td>
                        <td>
                            @include('bibliotheque._disponibilite', ['document' => $d])
                            @if ($d->estNumerique())<span class="statut statut-info"><i class="bi bi-file-earmark-pdf" style="font-size:.8rem"></i> PDF{{ $d->telechargeable ? '' : ' · en ligne' }}</span>@endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5"class="p-4 text-gris">Aucun document. <a href="{{ route('admin.documents.create') }}">Ajoutez le premier</a>.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($documents->hasPages())<div class="p-3">{{ $documents->links() }}</div>@endif
</div>
@endsection
