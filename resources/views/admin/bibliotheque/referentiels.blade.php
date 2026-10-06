@extends('layouts.admin')
@section('titre', 'Types, catégories et paramètres de prêt')

@section('contenu')
<div class="row g-3">
    {{-- Types de documents --}}
    <div class="col-xl-6">
        <div class="admin-carte p-3 p-lg-4 h-100">
            <h2 class="h5 mb-3">Types de documents</h2>
            <ul class="list-unstyled mb-3">
                @foreach ($types as $t)
                    <li class="border-bottom py-2">
                        <form method="post" action="{{ route('admin.types-documents.update', $t) }}" class="d-flex flex-wrap align-items-center gap-2">
                            @csrf @method('PUT')
                            <input name="nom" value="{{ $t->nom }}" class="form-control form-control-sm" style="max-width: 200px" required maxlength="60" aria-label="Nom">
                            <input name="ordre" type="number" value="{{ $t->ordre }}" class="form-control form-control-sm" style="max-width: 70px" min="0" aria-label="Ordre">
                            <div class="form-check small mb-0">
                                <input type="hidden" name="actif" value="0">
                                <input class="form-check-input" type="checkbox" name="actif" value="1" id="actif-{{ $t->id }}" @checked($t->actif)>
                                <label class="form-check-label" for="actif-{{ $t->id }}">Actif</label>
                            </div>
                            <span class="small text-gris">{{ $t->documents_count }} doc.</span>
                            <button class="btn btn-sm btn-outline-primary ms-auto" title="Enregistrer"><i class="bi bi-check-lg"></i></button>
                        </form>
                        @if (! $t->documents_count)
                            <form method="post" action="{{ route('admin.types-documents.destroy', $t) }}" class="d-inline" data-confirmer="Supprimer le type « {{ $t->nom }} » ?">
                                @csrf @method('DELETE')
                                <button class="btn btn-link btn-sm text-danger p-0">Supprimer</button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
            <form method="post" action="{{ route('admin.types-documents.store') }}" class="d-flex gap-2">
                @csrf
                <input name="nom" class="form-control form-control-sm" placeholder="Nouveau type (ex. : Thèse)" required maxlength="60">
                <input type="hidden" name="actif" value="1">
                <button class="btn btn-sm btn-primary text-nowrap"><i class="bi bi-plus-lg"></i> Ajouter</button>
            </form>
        </div>
    </div>

    {{-- Catégories --}}
    <div class="col-xl-6">
        <div class="admin-carte p-3 p-lg-4 h-100">
            <h2 class="h5 mb-3">Catégories / domaines</h2>
            <ul class="list-unstyled mb-3">
                @forelse ($categories as $c)
                    <li class="border-bottom py-2">
                        <form method="post" action="{{ route('admin.categories.update', $c) }}" class="d-flex flex-wrap align-items-center gap-2">
                            @csrf @method('PUT')
                            <input name="nom" value="{{ $c->nom }}" class="form-control form-control-sm" style="max-width: 240px" required maxlength="100" aria-label="Nom">
                            <span class="small text-gris">{{ $c->documents_count }} doc.</span>
                            <button class="btn btn-sm btn-outline-primary ms-auto" title="Enregistrer"><i class="bi bi-check-lg"></i></button>
                        </form>
                        <form method="post" action="{{ route('admin.categories.destroy', $c) }}" class="d-inline" data-confirmer="Supprimer la catégorie « {{ $c->nom }} » ? Les documents concernés resteront sans catégorie.">
                            @csrf @method('DELETE')
                            <button class="btn btn-link btn-sm text-danger p-0">Supprimer</button>
                        </form>
                    </li>
                @empty
                    <li class="small text-gris">Aucune catégorie.</li>
                @endforelse
            </ul>
            <form method="post" action="{{ route('admin.categories.store') }}" class="d-flex gap-2">
                @csrf
                <input name="nom" class="form-control form-control-sm" placeholder="Nouvelle catégorie (ex. : Énergie solaire)" required maxlength="100">
                <button class="btn btn-sm btn-primary text-nowrap"><i class="bi bi-plus-lg"></i> Ajouter</button>
            </form>
        </div>
    </div>

    {{-- Paramètres de prêt --}}
    <div class="col-12">
        <form method="post" action="{{ route('admin.parametres.update') }}" class="admin-carte p-3 p-lg-4">
            @csrf @method('PUT')
            <h2 class="h5 mb-3">Paramètres de prêt</h2>
            <div class="table-responsive mb-3">
                <table class="table align-middle mb-0" style="max-width: 560px">
                    <thead><tr><th>Type d'adhérent</th><th>Durée du prêt (jours)</th><th>Prêts simultanés max.</th></tr></thead>
                    <tbody>
                        @foreach ($typesAdherents as $t)
                            <tr>
                                <td>{{ $t->libelle() }}</td>
                                <td><input type="number" name="types[{{ $t->value }}][duree]" value="{{ old("types.{$t->value}.duree", $parametres['types'][$t->value]['duree']) }}" min="1" max="365" class="form-control form-control-sm" required aria-label="Durée {{ $t->libelle() }}"></td>
                                <td><input type="number" name="types[{{ $t->value }}][max]" value="{{ old("types.{$t->value}.max", $parametres['types'][$t->value]['max']) }}" min="0" max="50" class="form-control form-control-sm" required aria-label="Maximum {{ $t->libelle() }}"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row g-3" style="max-width: 900px">
                <div class="col-sm-6 col-lg-3">
                    <label for="max_prolongations" class="form-label small">Prolongations autorisées</label>
                    <input type="number" id="max_prolongations" name="max_prolongations" value="{{ $parametres['max_prolongations'] }}" min="0" max="10" class="form-control form-control-sm" required>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label for="delai_retrait" class="form-label small">Délai de retrait d'une réservation (j)</label>
                    <input type="number" id="delai_retrait" name="delai_retrait" value="{{ $parametres['delai_retrait'] }}" min="1" max="30" class="form-control form-control-sm" required>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label for="rappel_avant_echeance" class="form-label small">Rappel avant échéance (j)</label>
                    <input type="number" id="rappel_avant_echeance" name="rappel_avant_echeance" value="{{ $parametres['rappel_avant_echeance'] }}" min="0" max="30" class="form-control form-control-sm" required>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label for="relance_tous_les" class="form-label small">Relance des retards tous les (j)</label>
                    <input type="number" id="relance_tous_les" name="relance_tous_les" value="{{ $parametres['relance_tous_les'] }}" min="1" max="60" class="form-control form-control-sm" required>
                </div>
            </div>
            <button class="btn btn-primary mt-3"><i class="bi bi-check-lg me-1"></i>Enregistrer les paramètres</button>
        </form>
    </div>
</div>
@endsection
