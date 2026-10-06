@extends('layouts.admin')
@section('titre', $localisation->nom)

@section('contenu')
<a href="{{ route('admin.localisations.index') }}" class="small d-inline-block mb-3"><i class="bi bi-arrow-left me-1"></i>Plan de la bibliothèque</a>

<div class="row g-3">
    <div class="col-xl-8">
        <div class="admin-carte">
            <div class="p-3 p-lg-4 pb-0">
                <div class="small text-gris"><i class="bi {{ $localisation->type->icone() }} me-1"></i>{{ $localisation->type->libelle() }}</div>
                <h2 class="h5">{{ $localisation->chemin }}</h2>
                @if ($localisation->enfants->isNotEmpty())
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        @foreach ($localisation->enfants as $e)<a href="{{ route('admin.localisations.show', $e) }}" class="btn btn-sm btn-outline-primary">{{ $e->nom }}</a>@endforeach
                    </div>
                @endif
                <p class="small text-gris mb-2">{{ $exemplaires->total() }} exemplaire(s) dans cet espace et ses sous-espaces.</p>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Inventaire</th><th>Document</th><th>Emplacement</th><th>Statut</th></tr></thead>
                    <tbody>
                        @forelse ($exemplaires as $ex)
                            <tr>
                                <td><a href="{{ route('admin.exemplaires.show', $ex) }}"><code>{{ $ex->code_inventaire }}</code></a></td>
                                <td class="small">{{ $ex->document->titre }}</td>
                                <td class="small text-gris">{{ $ex->localisation->chemin }}</td>
                                <td><x-statut :statut="$ex->statut" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-4 text-gris">Aucun exemplaire.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($exemplaires->hasPages())<div class="p-3">{{ $exemplaires->links() }}</div>@endif
        </div>
    </div>
    <div class="col-xl-4">
        <form method="post" action="{{ route('admin.localisations.update', $localisation) }}" class="admin-carte p-3 p-lg-4 mb-3">
            @csrf @method('PUT')
            <h2 class="h6 mb-3">Modifier</h2>
            <label for="parent_id" class="form-label small">Dans</label>
            <select id="parent_id" name="parent_id" class="form-select form-select-sm mb-2 @error('parent_id') is-invalid @enderror">
                <option value="">— Niveau principal —</option>
                @foreach ($options as $id => $l)<option value="{{ $id }}" @selected((int) $localisation->parent_id === (int) $id)>{{ $l }}</option>@endforeach
            </select>
            <label for="nom" class="form-label small">Nom</label>
            <input id="nom" name="nom" value="{{ old('nom', $localisation->nom) }}" class="form-control form-control-sm mb-2" required maxlength="100">
            <div class="row g-2 mb-2">
                <div class="col-7">
                    <label for="type" class="form-label small">Type</label>
                    <select id="type" name="type" class="form-select form-select-sm">@foreach ($types as $t)<option value="{{ $t->value }}" @selected($localisation->type === $t)>{{ $t->libelle() }}</option>@endforeach</select>
                </div>
                <div class="col-5">
                    <label for="code" class="form-label small">Code</label>
                    <input id="code" name="code" value="{{ old('code', $localisation->code) }}" class="form-control form-control-sm" maxlength="30">
                </div>
            </div>
            <label for="description" class="form-label small">Description</label>
            <textarea id="description" name="description" rows="2" class="form-control form-control-sm mb-2" maxlength="500">{{ old('description', $localisation->description) }}</textarea>
            <button class="btn btn-sm btn-primary">Enregistrer</button>
        </form>
        <form method="post" action="{{ route('admin.localisations.destroy', $localisation) }}" data-confirmer="Supprimer l'espace « {{ $localisation->nom }} » ?" class="admin-carte p-3 p-lg-4">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Supprimer cet espace</button>
            <div class="form-text">Possible uniquement s'il est vide.</div>
        </form>
    </div>
</div>
@endsection
