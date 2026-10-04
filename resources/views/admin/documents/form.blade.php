@extends('layouts.admin')
@section('titre', $document->exists ? 'Modifier la notice' : 'Nouveau document')

@section('contenu')
<a href="{{ $document->exists ? route('admin.documents.show', $document) : route('admin.documents.index') }}" class="small d-inline-block mb-3"><i class="bi bi-arrow-left me-1"></i>Retour</a>

<form method="post" action="{{ $document->exists ? route('admin.documents.update', $document) : route('admin.documents.store') }}" class="admin-carte p-3 p-lg-4" style="max-width: 860px" enctype="multipart/form-data">
    @csrf
    @if ($document->exists) @method('PUT') @endif

    <div class="row g-3">
        <div class="col-12">
            <label for="titre" class="form-label">Titre</label>
            <input id="titre" name="titre" value="{{ old('titre', $document->titre) }}" class="form-control @error('titre') is-invalid @enderror" required maxlength="255">
        </div>
        <div class="col-md-8">
            <label for="auteurs" class="form-label">Auteur(s)</label>
            <input id="auteurs" name="auteurs" value="{{ old('auteurs', $document->auteurs) }}" class="form-control @error('auteurs') is-invalid @enderror" required maxlength="255" placeholder="Nom Prénom, Nom Prénom">
        </div>
        <div class="col-md-4">
            <label for="type" class="form-label">Type</label>
            <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                @foreach (\App\Enums\TypeDocument::cases() as $t)
                    <option value="{{ $t->value }}" @selected(old('type', $document->type?->value) === $t->value)>{{ $t->libelle() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="langue" class="form-label">Langue</label>
            <select id="langue" name="langue" class="form-select @error('langue') is-invalid @enderror" required>
                @foreach (config('acrest.langues') as $code => $libelle)
                    <option value="{{ $code }}" @selected(old('langue', $document->langue ?? 'fr') === $code)>{{ $libelle }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-5">
            <label for="editeur" class="form-label">Éditeur <span class="facultatif">(facultatif)</span></label>
            <input id="editeur" name="editeur" value="{{ old('editeur', $document->editeur) }}" class="form-control @error('editeur') is-invalid @enderror" maxlength="255">
        </div>
        <div class="col-md-3">
            <label for="annee_publication" class="form-label">Année <span class="facultatif">(facultatif)</span></label>
            <input id="annee_publication" name="annee_publication" type="number" value="{{ old('annee_publication', $document->annee_publication) }}" class="form-control @error('annee_publication') is-invalid @enderror" min="1800" max="{{ now()->year + 1 }}">
        </div>
        <div class="col-md-4">
            <label for="isbn" class="form-label">ISBN <span class="facultatif">(facultatif)</span></label>
            <input id="isbn" name="isbn" value="{{ old('isbn', $document->isbn) }}" class="form-control @error('isbn') is-invalid @enderror" maxlength="20">
        </div>
        <div class="col-md-4">
            <label for="cote" class="form-label">Cote <span class="facultatif">(facultatif)</span></label>
            <input id="cote" name="cote" value="{{ old('cote', $document->cote) }}" class="form-control @error('cote') is-invalid @enderror" maxlength="30" placeholder="621.31 KAM">
        </div>
        <div class="col-md-8">
            <label for="filiere_id" class="form-label">Filière concernée <span class="facultatif">(facultatif)</span></label>
            <select id="filiere_id" name="filiere_id" class="form-select @error('filiere_id') is-invalid @enderror">
                <option value="">Toutes / générale</option>
                @foreach ($filieres as $f)
                    <option value="{{ $f->id }}" @selected((string) old('filiere_id', $document->filiere_id) === (string) $f->id)>{{ $f->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <label for="resume" class="form-label">Résumé <span class="facultatif">(facultatif)</span></label>
            <textarea id="resume" name="resume" rows="4" class="form-control @error('resume') is-invalid @enderror" maxlength="3000">{{ old('resume', $document->resume) }}</textarea>
        </div>
        @unless ($document->exists)
            <div class="col-md-4">
                <label for="exemplaires" class="form-label">Nombre d'exemplaires</label>
                <input id="exemplaires" name="exemplaires" type="number" value="{{ old('exemplaires', 1) }}" class="form-control @error('exemplaires') is-invalid @enderror" min="0" max="50" required>
                <div class="form-text">Un code-barres est généré pour chacun. Mettez 0 pour un document uniquement numérique.</div>
            </div>
        @endunless
        <div class="col-12">
            <fieldset class="border rounded p-3">
                <legend class="float-none w-auto px-2 fs-6 mb-0"><i class="bi bi-file-earmark-pdf me-1"></i>Version numérique <span class="facultatif">(facultatif)</span></legend>
                @if ($document->estNumerique())
                    <p class="small mb-2">
                        PDF actuel : <a href="{{ route('bibliotheque.lire', $document) }}" target="_blank">consulter</a>
                        {{ $document->taille_fichier ? '('.$document->taille_fichier.')' : '' }}
                    </p>
                @endif
                <label for="fichier" class="form-label small">{{ $document->estNumerique() ? 'Remplacer le PDF' : 'Fichier PDF' }}</label>
                <input type="file" id="fichier" name="fichier" accept="application/pdf,.pdf" class="form-control @error('fichier') is-invalid @enderror">
                <div class="form-text">PDF uniquement, {{ config('acrest.bibliotheque.pdf_max_mo') }} Mo maximum. Le fichier n'est accessible qu'aux étudiants connectés.</div>
                <div class="form-check mt-3">
                    <input type="hidden" name="telechargeable" value="0">
                    <input class="form-check-input" type="checkbox" id="telechargeable" name="telechargeable" value="1" @checked(old('telechargeable', $document->telechargeable))>
                    <label class="form-check-label" for="telechargeable">Autoriser le téléchargement (sinon : lecture en ligne uniquement)</label>
                </div>
                @if ($document->estNumerique())
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="supprimer_fichier" name="supprimer_fichier" value="1">
                        <label class="form-check-label text-danger" for="supprimer_fichier">Supprimer la version numérique</label>
                    </div>
                @endif
            </fieldset>
        </div>
        <div class="col-12">
            <div class="form-check">
                <input type="hidden" name="consultation_sur_place" value="0">
                <input class="form-check-input" type="checkbox" id="consultation_sur_place" name="consultation_sur_place" value="1" @checked(old('consultation_sur_place', $document->consultation_sur_place))>
                <label class="form-check-label" for="consultation_sur_place">Consultation sur place uniquement (exclu du prêt)</label>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
    </div>
</form>
@endsection
