@extends('layouts.admin')
@section('titre', 'Adhérents')

@section('contenu')
<form method="get" class="admin-carte p-3 mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-lg-5">
            <label for="q" class="form-label small">Recherche</label>
            <input type="search" id="q" name="q" value="{{ $filtres['q'] ?? '' }}" class="form-control" placeholder="Matricule, nom, e-mail, téléphone…">
        </div>
        <div class="col-6 col-lg-2">
            <label for="type" class="form-label small">Type</label>
            <select id="type" name="type" class="form-select">
                <option value="">Tous</option>
                @foreach ($types as $t)<option value="{{ $t->value }}" @selected(($filtres['type'] ?? '') === $t->value)>{{ $t->libelle() }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-lg-2">
            <label for="statut" class="form-label small">Statut</label>
            <select id="statut" name="statut" class="form-select">
                <option value="">Tous</option>
                @foreach ($statuts as $s)<option value="{{ $s->value }}" @selected(($filtres['statut'] ?? '') === $s->value)>{{ $s->libelle() }}</option>@endforeach
            </select>
        </div>
        <div class="col-lg-3 d-flex gap-2">
            <button class="btn btn-outline-primary flex-fill"><i class="bi bi-funnel"></i> Filtrer</button>
            <a href="{{ route('admin.adherents.create') }}" class="btn btn-primary flex-fill"><i class="bi bi-person-plus"></i> Ajouter</a>
        </div>
    </div>
</form>

<form method="post" action="{{ route('admin.adherents.importer') }}" class="admin-carte p-3 mb-3 d-flex flex-wrap align-items-center gap-2 small" data-confirmer="Créer une fiche adhérent pour chaque étudiant en règle qui n'en a pas encore ?">
    @csrf
    <i class="bi bi-mortarboard text-foret fs-5"></i>
    <span class="me-auto">Importer en une fois tous les étudiants en règle — dossier validé et frais payés (matricule = code d'inscription).</span>
    <label for="date_expiration" class="text-gris">Adhésion jusqu'au</label>
    <input type="date" id="date_expiration" name="date_expiration" value="{{ today()->addYear()->toDateString() }}" class="form-control form-control-sm" style="max-width: 160px">
    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-download me-1"></i>Importer</button>
</form>

<div class="admin-carte">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Matricule</th><th>Adhérent</th><th>Type</th><th>Expiration</th><th>Prêts en cours</th><th>Statut</th></tr></thead>
            <tbody>
                @forelse ($adherents as $a)
                    <tr>
                        <td><code>{{ $a->matricule }}</code></td>
                        <td><a href="{{ route('admin.adherents.show', $a) }}" class="fw-semibold text-decoration-none">{{ $a->nom_complet }}</a><div class="small text-gris">{{ $a->email }}{{ $a->telephone ? ' · '.$a->telephone : '' }}</div></td>
                        <td class="small">{{ $a->type->libelle() }}</td>
                        <td class="small">{{ $a->date_expiration?->format('d/m/Y') ?? '—' }}</td>
                        <td class="small">{{ $a->prets_en_cours_count }}</td>
                        <td><x-statut :statut="$a->statutEffectif()" /></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-4 text-gris">Aucun adhérent.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($adherents->hasPages())<div class="p-3">{{ $adherents->links() }}</div>@endif
</div>
@endsection
