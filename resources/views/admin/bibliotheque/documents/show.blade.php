@extends('layouts.admin')
@section('titre', $document->titre)

@section('contenu')
<a href="{{ route('admin.documents.index') }}" class="small d-inline-block mb-3"><i class="bi bi-arrow-left me-1"></i>Tous les documents</a>

<div class="row g-3">
    <div class="col-xl-8">
        {{-- Notice --}}
        <div class="admin-carte p-3 p-lg-4 mb-3">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                <div>
                    <div class="small text-gris text-uppercase fw-semibold">{{ $document->type->nom }}{{ $document->categorie ? ' · '.$document->categorie->nom : '' }}</div>
                    <h2 class="h3 mb-1">{{ $document->titre }}</h2>
                    @if ($document->sous_titre)<div class="fs-5 text-gris">{{ $document->sous_titre }}</div>@endif
                    <div class="mt-1">{{ $document->noms_auteurs ?: 'Auteur non renseigné' }}</div>
                </div>
                <div class="d-flex flex-wrap gap-1 align-self-start">@include('bibliotheque._disponibilite', ['document' => $document])</div>
            </div>
            <div class="recap">
                <dl>
                    <dt>Éditeur</dt><dd>{{ $document->editeur ?: '—' }}{{ $document->annee_publication ? ', '.$document->annee_publication : '' }}</dd>
                    <dt>ISBN / ISSN</dt><dd>{{ $document->isbn ?: '—' }}</dd>
                    <dt>Langue</dt><dd>{{ $document->langue_libelle }}</dd>
                    <dt>Pages</dt><dd>{{ $document->nombre_pages ?: '—' }}</dd>
                    <dt>Cote</dt><dd>{{ $document->cote ?: '—' }}</dd>
                    <dt>Mots-clés</dt><dd>@forelse ($document->listeMotsCles() as $m)<span class="badge text-bg-light border me-1">{{ $m }}</span>@empty — @endforelse</dd>
                    <dt>Prêt</dt><dd>{{ $document->consultation_sur_place ? 'Consultation sur place uniquement' : 'Empruntable' }}</dd>
                </dl>
            </div>
            @if ($document->description)<p class="mt-3 mb-0">{!! nl2br(e($document->description)) !!}</p>@endif
        </div>

        {{-- Exemplaires --}}
        <div class="admin-carte mb-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-3 p-lg-4 pb-0">
                <h2 class="h5 mb-0">Exemplaires physiques ({{ $document->exemplaires->count() }})</h2>
                <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#ajout-exemplaires"><i class="bi bi-plus-lg me-1"></i>Ajouter des exemplaires</button>
            </div>
            <form method="post" action="{{ route('admin.exemplaires.store', $document) }}" class="collapse {{ $errors->hasAny(['nombre', 'code_barres', 'etat_physique', 'date_acquisition']) ? 'show' : '' }} px-3 px-lg-4 pt-3" id="ajout-exemplaires">
                @csrf
                <div class="row g-2 align-items-end p-3 rounded bg-light">
                    <div class="col-md-2"><label class="form-label small" for="nombre">Nombre</label><input type="number" id="nombre" name="nombre" value="1" min="1" max="50" class="form-control form-control-sm" required></div>
                    <div class="col-md-4"><label class="form-label small" for="loc-ajout">Localisation</label>
                        <select id="loc-ajout" name="localisation_id" class="form-select form-select-sm"><option value="">—</option>@foreach ($localisations as $id => $l)<option value="{{ $id }}">{{ $l }}</option>@endforeach</select></div>
                    <div class="col-md-3"><label class="form-label small" for="etat-ajout">État</label>
                        <select id="etat-ajout" name="etat_physique" class="form-select form-select-sm">@foreach ($etats as $e)<option value="{{ $e->value }}" @selected($e->value === 'neuf')>{{ $e->libelle() }}</option>@endforeach</select></div>
                    <div class="col-md-3"><label class="form-label small" for="date-ajout">Acquis le</label><input type="date" id="date-ajout" name="date_acquisition" value="{{ today()->toDateString() }}" max="{{ today()->toDateString() }}" class="form-control form-control-sm"></div>
                    <div class="col-md-5"><label class="form-label small" for="source-ajout">Source</label><input id="source-ajout" name="source_acquisition" class="form-control form-control-sm" placeholder="Achat, don…" maxlength="100"></div>
                    <div class="col-md-4"><label class="form-label small" for="cb-ajout">Code-barres <span class="facultatif">(1 exemplaire)</span></label><input id="cb-ajout" name="code_barres" class="form-control form-control-sm" maxlength="50"></div>
                    <div class="col-md-3"><button class="btn btn-sm btn-primary w-100">Ajouter</button></div>
                </div>
            </form>
            <div class="table-responsive mt-3">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Inventaire</th><th>Localisation</th><th>État</th><th>Statut</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                        @forelse ($document->exemplaires as $ex)
                            <tr>
                                <td><a href="{{ route('admin.exemplaires.show', $ex) }}" class="fw-semibold"><code>{{ $ex->code_inventaire }}</code></a>@if ($ex->code_barres)<div class="small text-gris">{{ $ex->code_barres }}</div>@endif</td>
                                <td class="small">{{ $ex->localisation?->chemin ?? '—' }}</td>
                                <td><x-statut :statut="$ex->etat_physique" /></td>
                                <td>
                                    <x-statut :statut="$ex->statut" />
                                    @if ($ex->empruntActif)<div class="small text-gris mt-1">{{ $ex->empruntActif->adherent->nom_complet }} · retour {{ $ex->empruntActif->date_retour_prevue->format('d/m') }}</div>@endif
                                </td>
                                <td class="text-end text-nowrap">
                                    @if ($ex->estPretable() && ! $document->consultation_sur_place)
                                        <a href="{{ route('admin.emprunts.create', ['exemplaire' => $ex->code_inventaire]) }}" class="btn btn-sm btn-soleil" title="Prêter"><i class="bi bi-box-arrow-right"></i></a>
                                    @endif
                                    <a href="{{ route('admin.exemplaires.show', $ex) }}" class="btn btn-sm btn-outline-primary" title="Fiche et historique"><i class="bi bi-clock-history"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-4 text-gris">Aucun exemplaire physique.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Prêts --}}
        <div class="admin-carte mb-3">
            <h2 class="h5 p-3 p-lg-4 pb-0 mb-3">Derniers prêts</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Adhérent</th><th>Exemplaire</th><th>Prêt</th><th>Retour</th><th>État</th></tr></thead>
                    <tbody>
                        @forelse ($emprunts as $e)
                            <tr>
                                <td><a href="{{ route('admin.adherents.show', $e->adherent) }}">{{ $e->adherent->nom_complet }}</a><div class="small text-gris">{{ $e->adherent->matricule }}</div></td>
                                <td class="small">{{ $e->exemplaire?->code_inventaire ?? '—' }}</td>
                                <td class="small text-nowrap">{{ $e->date_pret?->format('d/m/Y') ?? $e->created_at->format('d/m/Y') }}</td>
                                <td class="small text-nowrap">{{ $e->date_retour?->format('d/m/Y') ?? ($e->date_retour_prevue ? 'prévu '.$e->date_retour_prevue->format('d/m/Y') : '—') }}</td>
                                <td><x-statut :statut="$e->statut" />@if ($e->joursDeRetard())<div class="small text-danger">{{ $e->joursDeRetard() }} j de retard</div>@endif</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-4 text-gris">Ce document n'a jamais été prêté.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Historique --}}
        <div class="admin-carte">
            <h2 class="h5 p-3 p-lg-4 pb-0 mb-3">Historique</h2>
            @include('admin.bibliotheque._journal', ['activites' => $historique])
        </div>
    </div>

    <div class="col-xl-4">
        {{-- Ressources numériques --}}
        <div class="admin-carte p-3 p-lg-4 mb-3">
            <h2 class="h5 mb-3"><i class="bi bi-cloud me-1"></i>Ressources numériques</h2>
            @forelse ($document->ressources as $r)
                <div class="border rounded p-2 mb-2">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi {{ $r->type->icone() }} fs-4 text-foret"></i>
                        <div class="flex-fill">
                            <div class="fw-semibold small">{{ $r->libelle }}</div>
                            <div class="small text-gris">{{ $r->nom_original }} · {{ $r->taille_lisible }} · v{{ $r->version }} · {{ $r->created_at->format('d/m/Y') }}</div>
                            <x-statut :statut="$r->niveau_acces" class="small mt-1" />
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-1 mt-2">
                        @if ($r->type->lisibleEnLigne())<a href="{{ route('bibliotheque.ressources.consulter', $r) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Ouvrir</a>@endif
                        <a href="{{ route('bibliotheque.ressources.telecharger', $r) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i></a>
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#droits-{{ $r->id }}"><i class="bi bi-shield-lock"></i> Droits</button>
                        <form method="post" action="{{ route('admin.ressources.destroy', $r) }}" data-confirmer="Supprimer définitivement ce fichier ?" class="ms-auto">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                    <form method="post" action="{{ route('admin.ressources.update', $r) }}" class="collapse mt-2" id="droits-{{ $r->id }}">
                        @csrf @method('PATCH')
                        <input name="titre" value="{{ $r->titre }}" class="form-control form-control-sm mb-2" placeholder="Intitulé (facultatif)">
                        <input name="version" value="{{ $r->version }}" class="form-control form-control-sm mb-2" placeholder="Version">
                        <select name="niveau_acces" class="form-select form-select-sm mb-2">@foreach ($niveaux as $n)<option value="{{ $n->value }}" @selected($r->niveau_acces === $n)>{{ $n->libelle() }}</option>@endforeach</select>
                        <button class="btn btn-sm btn-primary">Enregistrer</button>
                    </form>
                </div>
            @empty
                <p class="small text-gris">Aucune ressource numérique.</p>
            @endforelse

            <form method="post" action="{{ route('admin.ressources.store', $document) }}" enctype="multipart/form-data" class="mt-3 pt-3 border-top">
                @csrf
                <h3 class="h6">Ajouter un fichier</h3>
                <input type="file" name="fichier" class="form-control form-control-sm mb-2 @error('fichier') is-invalid @enderror" required accept=".{{ implode(',.', \App\Enums\TypeRessource::EXTENSIONS) }}">
                @error('fichier')<div class="invalid-feedback d-block mb-2">{{ $message }}</div>@enderror
                <div class="form-text mb-2">PDF, EPUB, Word, audio ou vidéo · {{ config('acrest.bibliotheque.fichier_max_mo') }} Mo max.</div>
                <div class="row g-2 mb-2">
                    <div class="col-8"><input name="titre" value="{{ old('titre') }}" class="form-control form-control-sm" placeholder="Intitulé (facultatif)"></div>
                    <div class="col-4"><input name="version" value="{{ old('version', '1') }}" class="form-control form-control-sm" placeholder="Version"></div>
                </div>
                <label for="niveau_acces" class="form-label small">Niveau d'accès</label>
                <select id="niveau_acces" name="niveau_acces" class="form-select form-select-sm mb-2">
                    @foreach ($niveaux as $n)<option value="{{ $n->value }}" @selected(old('niveau_acces', 'consultation') === $n->value)>{{ $n->libelle() }}</option>@endforeach
                </select>
                <button class="btn btn-sm btn-primary w-100"><i class="bi bi-upload me-1"></i>Envoyer</button>
            </form>
        </div>

        <div class="admin-carte p-3 p-lg-4">
            <h2 class="h6 mb-3">Actions</h2>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.documents.edit', $document) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Modifier la notice</a>
                <a href="{{ route('bibliotheque.show', $document) }}" class="btn btn-outline-primary btn-sm" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>Voir sur le site</a>
                <form method="post" action="{{ route('admin.documents.destroy', $document) }}" data-confirmer="Supprimer « {{ $document->titre }} » du catalogue ?">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i>Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
