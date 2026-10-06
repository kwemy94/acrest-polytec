@extends('layouts.admin')
@section('titre', 'Exemplaire '.$exemplaire->code_inventaire)

@section('contenu')
<a href="{{ route('admin.documents.show', $exemplaire->document) }}" class="small d-inline-block mb-3"><i class="bi bi-arrow-left me-1"></i>{{ $exemplaire->document->titre }}</a>

<div class="row g-3">
    <div class="col-xl-8">
        <div class="admin-carte p-3 p-lg-4 mb-3">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                <div>
                    <div class="code-inscription fs-4 mb-2">{{ $exemplaire->code_inventaire }}</div>
                    <h2 class="h5 mb-0">{{ $exemplaire->document->titre_complet }}</h2>
                    <div class="text-gris">{{ $exemplaire->document->noms_auteurs }}</div>
                </div>
                <div class="d-flex flex-column gap-1 align-items-end">
                    <x-statut :statut="$exemplaire->statut" />
                    <x-statut :statut="$exemplaire->etat_physique" />
                </div>
            </div>
            <div class="p-3 rounded bg-light mb-3">
                <div class="small text-gris mb-1"><i class="bi bi-geo-alt me-1"></i>Localisation actuelle</div>
                <div class="fw-semibold">{{ $exemplaire->localisation?->chemin ?? 'Non localisé' }}</div>
            </div>
            @if ($exemplaire->empruntActif)
                <div class="alert alert-warning small mb-0">
                    En prêt chez <a href="{{ route('admin.adherents.show', $exemplaire->empruntActif->adherent) }}">{{ $exemplaire->empruntActif->adherent->nom_complet }}</a>
                    depuis le {{ $exemplaire->empruntActif->date_pret->format('d/m/Y') }}, retour prévu le {{ $exemplaire->empruntActif->date_retour_prevue->format('d/m/Y') }}.
                    <a href="{{ route('admin.emprunts.retours', ['code' => $exemplaire->code_inventaire]) }}" class="ms-1">Enregistrer le retour</a>
                </div>
            @endif
        </div>

        {{-- Historique des déplacements --}}
        <div class="admin-carte mb-3">
            <h2 class="h5 p-3 p-lg-4 pb-0 mb-3">Historique des déplacements</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Date</th><th>Ancienne localisation</th><th>Nouvelle localisation</th><th>Par</th></tr></thead>
                    <tbody>
                        @forelse ($exemplaire->historiqueLocalisations as $h)
                            <tr>
                                <td class="small text-nowrap">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                                <td class="small">{{ $h->ancienne?->chemin ?? '—' }}</td>
                                <td class="small">{{ $h->nouvelle?->chemin ?? '—' }}@if ($h->motif)<div class="text-gris">{{ $h->motif }}</div>@endif</td>
                                <td class="small">{{ $h->user?->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-4 text-gris">Aucun déplacement enregistré.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Prêts --}}
        <div class="admin-carte mb-3">
            <h2 class="h5 p-3 p-lg-4 pb-0 mb-3">Prêts</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Adhérent</th><th>Prêt</th><th>Retour prévu</th><th>Retour effectif</th><th>État</th></tr></thead>
                    <tbody>
                        @forelse ($exemplaire->emprunts as $e)
                            <tr>
                                <td><a href="{{ route('admin.adherents.show', $e->adherent) }}">{{ $e->adherent->nom_complet }}</a></td>
                                <td class="small">{{ $e->date_pret?->format('d/m/Y') ?? '—' }}</td>
                                <td class="small">{{ $e->date_retour_prevue?->format('d/m/Y') ?? '—' }}</td>
                                <td class="small">{{ $e->date_retour?->format('d/m/Y') ?? '—' }}@if ($e->etat_retour)<div class="text-gris">{{ $e->etat_retour->libelle() }}</div>@endif</td>
                                <td><x-statut :statut="$e->statut" />@if ($e->joursDeRetard())<div class="small text-danger">{{ $e->joursDeRetard() }} j de retard</div>@endif</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-4 text-gris">Jamais prêté.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Incidents --}}
        @if ($exemplaire->incidents->isNotEmpty())
            <div class="admin-carte mb-3">
                <h2 class="h5 p-3 p-lg-4 pb-0 mb-3">Incidents</h2>
                <ul class="list-unstyled px-3 px-lg-4 pb-3 mb-0">
                    @foreach ($exemplaire->incidents as $i)
                        <li class="mb-2 small">
                            <x-statut :statut="$i->type" class="small" />
                            {{ $i->description }}
                            <span class="text-gris">· {{ $i->created_at->format('d/m/Y') }}{{ $i->adherent ? ' · '.$i->adherent->nom_complet : '' }}{{ $i->user ? ' · '.$i->user->name : '' }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="admin-carte">
            <h2 class="h5 p-3 p-lg-4 pb-0 mb-3">Journal</h2>
            @include('admin.bibliotheque._journal', ['activites' => $journal])
        </div>
    </div>

    <div class="col-xl-4">
        <div class="admin-carte p-3 p-lg-4 mb-3">
            <h2 class="h6 mb-3"><i class="bi bi-geo-alt me-1"></i>Changer de localisation</h2>
            <form method="post" action="{{ route('admin.exemplaires.deplacer', $exemplaire) }}">
                @csrf @method('PATCH')
                <select name="localisation_id" class="form-select form-select-sm mb-2" aria-label="Nouvelle localisation">
                    <option value="">— Sans localisation —</option>
                    @foreach ($localisations as $id => $l)<option value="{{ $id }}" @selected((int) $exemplaire->localisation_id === (int) $id)>{{ $l }}</option>@endforeach
                </select>
                <input name="motif" class="form-control form-control-sm mb-2" placeholder="Motif (facultatif) : réorganisation, magasin…" maxlength="255">
                <button class="btn btn-sm btn-primary">Déplacer</button>
            </form>
        </div>

        <div class="admin-carte p-3 p-lg-4 mb-3">
            <h2 class="h6 mb-3"><i class="bi bi-flag me-1"></i>Changer le statut</h2>
            @if ($exemplaire->statut->enCirculation())
                <p class="small text-gris mb-0">Statut géré par la circulation ({{ $exemplaire->statut->libelle() }}) : enregistrez d'abord le retour ou annulez la réservation.</p>
            @else
                <form method="post" action="{{ route('admin.exemplaires.statut', $exemplaire) }}">
                    @csrf @method('PATCH')
                    <select name="statut" class="form-select form-select-sm mb-2" aria-label="Statut">
                        @foreach ($statutsManuels as $s)<option value="{{ $s->value }}" @selected($exemplaire->statut === $s)>{{ $s->libelle() }}</option>@endforeach
                    </select>
                    <input name="motif" class="form-control form-control-sm mb-2" placeholder="Motif (perte, réparation…)" maxlength="500">
                    <button class="btn btn-sm btn-primary">Appliquer</button>
                </form>
            @endif
        </div>

        <div class="admin-carte p-3 p-lg-4 mb-3">
            <h2 class="h6 mb-3"><i class="bi bi-pencil me-1"></i>Informations</h2>
            <form method="post" action="{{ route('admin.exemplaires.update', $exemplaire) }}">
                @csrf @method('PUT')
                <label class="form-label small" for="code_barres">Code-barres / QR</label>
                <input id="code_barres" name="code_barres" value="{{ old('code_barres', $exemplaire->code_barres) }}" class="form-control form-control-sm mb-2 @error('code_barres') is-invalid @enderror" maxlength="50">
                <label class="form-label small" for="etat_physique">État physique</label>
                <select id="etat_physique" name="etat_physique" class="form-select form-select-sm mb-2">@foreach ($etats as $e)<option value="{{ $e->value }}" @selected($exemplaire->etat_physique === $e)>{{ $e->libelle() }}</option>@endforeach</select>
                <label class="form-label small" for="date_acquisition">Date d'acquisition</label>
                <input id="date_acquisition" type="date" name="date_acquisition" value="{{ old('date_acquisition', $exemplaire->date_acquisition?->toDateString()) }}" max="{{ today()->toDateString() }}" class="form-control form-control-sm mb-2">
                <label class="form-label small" for="source_acquisition">Source d'acquisition</label>
                <input id="source_acquisition" name="source_acquisition" value="{{ old('source_acquisition', $exemplaire->source_acquisition) }}" class="form-control form-control-sm mb-2" maxlength="100">
                <label class="form-label small" for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="2" class="form-control form-control-sm mb-2" maxlength="500">{{ old('notes', $exemplaire->notes) }}</textarea>
                <button class="btn btn-sm btn-primary">Enregistrer</button>
            </form>
        </div>

        <div class="admin-carte p-3 p-lg-4">
            <div class="d-flex flex-wrap gap-2">
                @if ($exemplaire->estPretable() && ! $exemplaire->document->consultation_sur_place)
                    <a href="{{ route('admin.emprunts.create', ['exemplaire' => $exemplaire->code_inventaire]) }}" class="btn btn-sm btn-soleil"><i class="bi bi-box-arrow-right me-1"></i>Prêter</a>
                @endif
                @if ($exemplaire->emprunts->isEmpty())
                    <form method="post" action="{{ route('admin.exemplaires.destroy', $exemplaire) }}" data-confirmer="Supprimer l'exemplaire {{ $exemplaire->code_inventaire }} ?">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Supprimer</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
