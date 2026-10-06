@extends('layouts.app')
@section('titre', $document->titre)

@section('contenu')
@include('partials.entete', [
    'titre' => $document->titre_complet,
    'intro' => $document->noms_auteurs,
    'ariane' => ['Bibliothèque' => route('bibliotheque.index'), $document->titre => null],
])

@php
    $enRayon = $document->exemplaires->filter(fn ($e) => in_array($e->statut, \App\Enums\StatutExemplaire::fondsActif(), true));
@endphp

<section class="section">
    <div class="container">
        @include('partials.flash')
        @include('bibliotheque._lecteur', ['lecteur' => $lecteur])

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="recap">
                    <div class="recap-entete"><h2>Notice</h2></div>
                    <dl>
                        <dt>Type</dt><dd>{{ $document->type->nom }}</dd>
                        @if ($document->categorie)<dt>Catégorie</dt><dd>{{ $document->categorie->nom }}</dd>@endif
                        <dt>Auteur(s)</dt><dd>{{ $document->noms_auteurs ?: '—' }}</dd>
                        <dt>Langue</dt><dd>{{ $document->langue_libelle }}</dd>
                        @if ($document->editeur)<dt>Éditeur</dt><dd>{{ $document->editeur }}{{ $document->annee_publication ? ', '.$document->annee_publication : '' }}</dd>@endif
                        @if ($document->isbn)<dt>ISBN / ISSN</dt><dd>{{ $document->isbn }}</dd>@endif
                        @if ($document->nombre_pages)<dt>Pages</dt><dd>{{ $document->nombre_pages }}</dd>@endif
                        @if ($document->cote)<dt>Cote</dt><dd>{{ $document->cote }}</dd>@endif
                        @if ($document->mots_cles)<dt>Mots-clés</dt><dd>@foreach ($document->listeMotsCles() as $m)<a href="{{ route('bibliotheque.index', ['q' => $m]) }}" class="badge text-bg-light border text-decoration-none me-1">{{ $m }}</a>@endforeach</dd>@endif
                    </dl>
                </div>
                @if ($document->description)
                    <div class="recap">
                        <div class="recap-entete"><h2>Description</h2></div>
                        <p class="p-3 mb-0">{!! nl2br(e($document->description)) !!}</p>
                    </div>
                @endif

                @if ($enRayon->isNotEmpty())
                    <div class="recap">
                        <div class="recap-entete"><h2>Exemplaires et localisation</h2></div>
                        <div class="table-responsive">
                            <table class="table mb-0 align-middle">
                                <thead><tr><th>Exemplaire</th><th>Localisation</th><th>Disponibilité</th></tr></thead>
                                <tbody>
                                    @foreach ($enRayon as $ex)
                                        <tr>
                                            <td><code>{{ $ex->code_inventaire }}</code></td>
                                            <td class="small">{{ $ex->localisation?->chemin ?? '—' }}</td>
                                            <td>
                                                <x-statut :statut="$ex->statut" />
                                                @if ($ex->empruntActif)<div class="small text-gris mt-1">Retour prévu le {{ $ex->empruntActif->date_retour_prevue->format('d/m/Y') }}</div>@endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                @if ($ressources->isNotEmpty())
                    <div class="encart encart-foret mb-3">
                        <h2 class="h5"><i class="bi bi-cloud me-1"></i>Version numérique</h2>
                        @foreach ($ressources as ['ressource' => $r, 'consulter' => $peutConsulter, 'telecharger' => $peutTelecharger])
                            <div class="py-2 {{ $loop->first ? '' : 'border-top border-light border-opacity-25' }}">
                                <div class="fw-semibold"><i class="bi {{ $r->type->icone() }} me-1"></i>{{ $r->libelle }}</div>
                                <div class="small text-white-50 mb-2">{{ $r->type->libelle() }} · {{ $r->taille_lisible }} · {{ $r->niveau_acces->libelle() }}</div>
                                @if ($peutConsulter || $peutTelecharger)
                                    <div class="d-flex flex-wrap gap-2">
                                        @if ($peutConsulter)<a href="{{ route('bibliotheque.ressources.consulter', $r) }}" class="btn btn-sm btn-soleil"><i class="bi bi-book-half me-1"></i>Consulter</a>@endif
                                        @if ($peutTelecharger)<a href="{{ route('bibliotheque.ressources.telecharger', $r) }}" class="btn btn-sm btn-outline-light"><i class="bi bi-download me-1"></i>Télécharger</a>@endif
                                    </div>
                                @elseif (! $lecteur && ! auth()->check())
                                    <a href="{{ route('bibliotheque.connexion') }}" class="btn btn-sm btn-soleil"><i class="bi bi-box-arrow-in-right me-1"></i>Se connecter pour y accéder</a>
                                @elseif ($lecteur && ! $lecteur->estActif())
                                    <p class="small mb-0">Accès suspendu : adhésion « {{ $lecteur->statutEffectif()->libelle() }} ».</p>
                                @else
                                    <p class="small mb-0">Ce format ne se lit pas en ligne et son téléchargement n'est pas autorisé : consultez-le à la bibliothèque.</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($demandable)
                    <div class="encart sticky-encart">
                        <h2 class="h5">Emprunter</h2>
                        <div class="d-flex flex-wrap gap-1 mb-3">@include('bibliotheque._disponibilite', ['document' => $document])</div>
                        @if ($enAttente > 0)<p class="small text-gris">{{ $enAttente }} demande{{ $enAttente > 1 ? 's' : '' }} en file d'attente.</p>@endif

                        @error('emprunt')<div class="alert alert-danger small">{{ $message }}</div>@enderror

                        @if (! $lecteur)
                            <a href="{{ route('bibliotheque.connexion') }}" class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right me-1"></i>Se connecter pour emprunter</a>
                        @elseif ($demandeEnCours)
                            <p class="small mb-2"><i class="bi bi-check-circle text-success me-1"></i>Vous avez déjà une demande ou un prêt en cours pour ce document.</p>
                            <a href="{{ route('bibliotheque.emprunts') }}" class="btn btn-outline-primary w-100">Voir mes emprunts</a>
                        @elseif (! $lecteur->estActif())
                            <p class="small mb-0">Votre adhésion est « {{ $lecteur->statutEffectif()->libelle() }} » : adressez-vous à la bibliothèque.</p>
                        @else
                            <form method="post" action="{{ route('bibliotheque.demander', $document) }}">
                                @csrf
                                <label for="message" class="form-label small">Message pour la bibliothèque <span class="facultatif">(facultatif)</span></label>
                                <textarea id="message" name="message" rows="2" maxlength="500" class="form-control mb-3" placeholder="Ex. : besoin pour un exposé le 15">{{ old('message') }}</textarea>
                                <button class="btn btn-soleil w-100" data-chargement="Envoi…">
                                    <i class="bi bi-bookmark-plus me-1"></i>{{ $document->disponibles_count > 0 ? 'Demander ce document' : 'Réserver (file d\'attente)' }}
                                </button>
                            </form>
                            <p class="small text-gris mt-3 mb-0">Vous serez prévenu par e-mail quand le document sera prêt à être retiré.</p>
                        @endif
                    </div>
                @elseif ($document->consultation_sur_place)
                    <div class="encart">
                        <h2 class="h5">Consultation sur place</h2>
                        <p class="small mb-0">Ce document se consulte uniquement à la bibliothèque, {{ config('acrest.bibliotheque.horaires') }}.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
