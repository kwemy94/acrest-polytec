@extends('layouts.app')
@section('titre', 'Bibliothèque')
@section('description', 'Catalogue de la bibliothèque d\'ACREST Polytechnique : livres, mémoires, revues et ressources numériques.')

@section('contenu')
@include('partials.entete', [
    'titre' => 'Bibliothèque',
    'intro' => 'Recherchez un document, vérifiez sa disponibilité et son emplacement, consultez les ressources numériques.',
    'ariane' => ['Bibliothèque' => null],
])

<section class="section">
    <div class="container">
        @include('partials.flash')
        @include('bibliotheque._lecteur', ['lecteur' => $lecteur])

        <form method="get" class="row g-2 align-items-end mb-4" role="search">
            <div class="col-lg-4">
                <label for="q" class="form-label small">Rechercher</label>
                <input type="search" id="q" name="q" value="{{ $filtres['q'] ?? '' }}" class="form-control" placeholder="Titre, auteur, ISBN/ISSN, mot-clé…">
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
                <label for="langue" class="form-label small">Langue</label>
                <select id="langue" name="langue" class="form-select">
                    <option value="">Toutes</option>
                    @foreach (config('acrest.langues') as $code => $libelle)<option value="{{ $code }}" @selected(($filtres['langue'] ?? '') === $code)>{{ $libelle }}</option>@endforeach
                </select>
            </div>
            <div class="col-6 col-lg-2"><button class="btn btn-primary w-100"><i class="bi bi-search"></i> Chercher</button></div>
            <div class="col-12 d-flex flex-wrap gap-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="disponible" name="disponible" value="1" @checked($filtres['disponible'] ?? false) onchange="this.form.submit()">
                    <label class="form-check-label" for="disponible">Disponibles au prêt</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="numerique" name="numerique" value="1" @checked($filtres['numerique'] ?? false) onchange="this.form.submit()">
                    <label class="form-check-label" for="numerique"><i class="bi bi-cloud me-1"></i>Avec version numérique</label>
                </div>
            </div>
        </form>

        <p class="text-gris small mb-3">{{ $documents->total() }} document{{ $documents->total() > 1 ? 's' : '' }}</p>

        <div class="row g-3">
            @forelse ($documents as $document)
                <div class="col-md-6 col-lg-4">
                    <article class="carte-doc">
                        <div class="type-doc">{{ $document->type->nom }}{{ $document->categorie ? ' · '.$document->categorie->nom : '' }}</div>
                        <h2><a href="{{ route('bibliotheque.show', $document) }}">{{ $document->titre }}</a></h2>
                        <p class="text-gris small mb-2">{{ $document->noms_auteurs }}{{ $document->annee_publication ? ' · '.$document->annee_publication : '' }} · {{ $document->langue_libelle }}</p>
                        <div class="mt-auto">
                            <div class="d-flex flex-wrap gap-1 mb-2">@include('bibliotheque._disponibilite', ['document' => $document])</div>
                            @include('bibliotheque._localisations', ['document' => $document])
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="encart text-center p-5">
                        <i class="bi bi-journal-x fs-1 text-gris"></i>
                        <p class="mt-2 mb-0">Aucun document ne correspond à votre recherche.</p>
                    </div>
                </div>
            @endforelse
        </div>

        @if ($documents->hasPages())<div class="mt-4">{{ $documents->links() }}</div>@endif
    </div>
</section>
@endsection
