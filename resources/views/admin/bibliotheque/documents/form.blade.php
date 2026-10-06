@extends('layouts.admin')
@section('titre', $document->exists ? 'Modifier la notice' : 'Nouveau document')

@section('contenu')
<a href="{{ $document->exists ? route('admin.documents.show', $document) : route('admin.documents.index') }}" class="small d-inline-block mb-3"><i class="bi bi-arrow-left me-1"></i>Retour</a>

@if ($types->isEmpty())
    <div class="alert alert-warning">Aucun type de document n'est configuré. @if (auth()->user()->estAdmin())<a href="{{ route('admin.referentiels') }}">Créez-en un</a>@else Demandez à un administrateur d'en créer un. @endif</div>
@endif

<form method="post" action="{{ $document->exists ? route('admin.documents.update', $document) : route('admin.documents.store') }}" class="admin-carte p-3 p-lg-4" style="max-width: 920px">
    @csrf
    @if ($document->exists) @method('PUT') @endif

    <h2 class="h5 mb-3">Description</h2>
    <div class="row g-3">
        <div class="col-md-7">
            <label for="titre" class="form-label">Titre</label>
            <input id="titre" name="titre" value="{{ old('titre', $document->titre) }}" class="form-control @error('titre') is-invalid @enderror" required maxlength="255">
        </div>
        <div class="col-md-5">
            <label for="sous_titre" class="form-label">Sous-titre <span class="facultatif">(facultatif)</span></label>
            <input id="sous_titre" name="sous_titre" value="{{ old('sous_titre', $document->sous_titre) }}" class="form-control @error('sous_titre') is-invalid @enderror" maxlength="255">
        </div>
        <div class="col-md-6">
            <label for="auteurs" class="form-label">Auteur(s)</label>
            <textarea id="auteurs" name="auteurs" rows="3" class="form-control @error('auteurs') is-invalid @enderror" placeholder="Un auteur par ligne&#10;Ex. : Anne Labouret&#10;Michel Villoz">{{ old('auteurs', $document->exists ? $document->auteurs->pluck('nom')->join("\n") : '') }}</textarea>
            <div class="form-text">Un auteur par ligne, dans l'ordre de la page de titre.</div>
        </div>
        <div class="col-md-6">
            <div class="row g-3">
                <div class="col-sm-6">
                    <label for="type_document_id" class="form-label">Type de document</label>
                    <select id="type_document_id" name="type_document_id" class="form-select @error('type_document_id') is-invalid @enderror" required>
                        @foreach ($types as $t)<option value="{{ $t->id }}" @selected((string) old('type_document_id', $document->type_document_id) === (string) $t->id)>{{ $t->nom }}</option>@endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    <label for="categorie_id" class="form-label">Catégorie / domaine</label>
                    <select id="categorie_id" name="categorie_id" class="form-select @error('categorie_id') is-invalid @enderror">
                        <option value="">—</option>
                        @foreach ($categories as $c)<option value="{{ $c->id }}" @selected((string) old('categorie_id', $document->categorie_id) === (string) $c->id)>{{ $c->nom }}</option>@endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    <label for="langue" class="form-label">Langue</label>
                    <select id="langue" name="langue" class="form-select @error('langue') is-invalid @enderror" required>
                        @foreach (config('acrest.langues') as $code => $libelle)<option value="{{ $code }}" @selected(old('langue', $document->langue ?? 'fr') === $code)>{{ $libelle }}</option>@endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    <label for="nombre_pages" class="form-label">Nombre de pages</label>
                    <input id="nombre_pages" name="nombre_pages" type="number" min="1" value="{{ old('nombre_pages', $document->nombre_pages) }}" class="form-control @error('nombre_pages') is-invalid @enderror">
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <label for="editeur" class="form-label">Éditeur</label>
            <input id="editeur" name="editeur" value="{{ old('editeur', $document->editeur) }}" class="form-control @error('editeur') is-invalid @enderror" maxlength="255">
        </div>
        <div class="col-md-2">
            <label for="annee_publication" class="form-label">Année</label>
            <input id="annee_publication" name="annee_publication" type="number" value="{{ old('annee_publication', $document->annee_publication) }}" class="form-control @error('annee_publication') is-invalid @enderror" min="1500" max="{{ now()->year + 1 }}">
        </div>
        <div class="col-md-3">
            <label for="isbn" class="form-label">ISBN / ISSN</label>
            <input id="isbn" name="isbn" value="{{ old('isbn', $document->isbn) }}" class="form-control @error('isbn') is-invalid @enderror" maxlength="20">
        </div>
        <div class="col-md-2">
            <label for="cote" class="form-label">Cote</label>
            <input id="cote" name="cote" value="{{ old('cote', $document->cote) }}" class="form-control @error('cote') is-invalid @enderror" maxlength="30" placeholder="621.3 LAB">
        </div>
        <div class="col-12">
            <label for="mots_cles" class="form-label">Mots-clés</label>
            <input id="mots_cles" name="mots_cles" value="{{ old('mots_cles', $document->mots_cles) }}" class="form-control @error('mots_cles') is-invalid @enderror" maxlength="500" placeholder="énergie solaire, photovoltaïque, dimensionnement">
            <div class="form-text">Séparés par des virgules.</div>
        </div>
        <div class="col-12">
            <label for="description" class="form-label">Description / résumé</label>
            <textarea id="description" name="description" rows="4" class="form-control @error('description') is-invalid @enderror" maxlength="5000">{{ old('description', $document->description) }}</textarea>
        </div>
        <div class="col-12">
            <div class="form-check">
                <input type="hidden" name="consultation_sur_place" value="0">
                <input class="form-check-input" type="checkbox" id="consultation_sur_place" name="consultation_sur_place" value="1" @checked(old('consultation_sur_place', $document->consultation_sur_place))>
                <label class="form-check-label" for="consultation_sur_place">Consultation sur place uniquement (exclu du prêt)</label>
            </div>
        </div>
    </div>

    @unless ($document->exists)
        <h2 class="h5 mt-4 mb-3">Exemplaires physiques</h2>
        <div class="row g-3">
            <div class="col-md-2">
                <label for="exemplaires" class="form-label">Nombre</label>
                <input id="exemplaires" name="exemplaires" type="number" value="{{ old('exemplaires', 1) }}" class="form-control @error('exemplaires') is-invalid @enderror" min="0" max="50" required>
            </div>
            <div class="col-md-5">
                <label for="localisation_id" class="form-label">Localisation</label>
                <select id="localisation_id" name="localisation_id" class="form-select @error('localisation_id') is-invalid @enderror">
                    <option value="">— À ranger plus tard —</option>
                    @foreach ($localisations as $id => $libelle)<option value="{{ $id }}" @selected((string) old('localisation_id') === (string) $id)>{{ $libelle }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label for="etat_physique" class="form-label">État</label>
                <select id="etat_physique" name="etat_physique" class="form-select">
                    @foreach (\App\Enums\EtatPhysique::cases() as $e)<option value="{{ $e->value }}" @selected(old('etat_physique', 'neuf') === $e->value)>{{ $e->libelle() }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="date_acquisition" class="form-label">Date d'acquisition</label>
                <input id="date_acquisition" name="date_acquisition" type="date" value="{{ old('date_acquisition', today()->toDateString()) }}" max="{{ today()->toDateString() }}" class="form-control @error('date_acquisition') is-invalid @enderror">
            </div>
            <div class="col-md-8">
                <label for="source_acquisition" class="form-label">Source d'acquisition</label>
                <input id="source_acquisition" name="source_acquisition" value="{{ old('source_acquisition') }}" class="form-control" maxlength="100" list="sources" placeholder="Achat, don, dépôt…">
                <datalist id="sources"><option value="Achat"><option value="Don"><option value="Dépôt légal"><option value="Échange"></datalist>
            </div>
            <div class="col-12 form-text mt-1">Chaque exemplaire reçoit un code d'inventaire unique ({{ config('acrest.bibliotheque.prefixe_inventaire') }}00001…). Mettez 0 pour un document uniquement numérique.</div>
        </div>
    @endunless

    <div class="mt-4">
        <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
    </div>
</form>
@endsection
