@extends('layouts.app')
@section('titre', $document->titre)

@section('contenu')
@include('partials.entete', [
    'titre' => $document->titre,
    'intro' => $document->auteurs,
    'ariane' => ['Bibliothèque' => route('bibliotheque.index'), $document->titre => null],
])

<section class="section">
    <div class="container">
        @include('partials.flash')
        @include('bibliotheque._lecteur', ['lecteur' => $lecteur])

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="recap">
                    <div class="recap-entete"><h2>Notice</h2></div>
                    <dl>
                        <dt>Type</dt><dd><i class="bi {{ $document->type->icone() }} me-1"></i>{{ $document->type->libelle() }}</dd>
                        <dt>Auteur(s)</dt><dd>{{ $document->auteurs }}</dd>
                        <dt>Langue</dt><dd>{{ $document->langue_libelle }}</dd>
                        @if ($document->editeur)<dt>Éditeur</dt><dd>{{ $document->editeur }}</dd>@endif
                        @if ($document->annee_publication)<dt>Année</dt><dd>{{ $document->annee_publication }}</dd>@endif
                        @if ($document->isbn)<dt>ISBN</dt><dd>{{ $document->isbn }}</dd>@endif
                        @if ($document->cote)<dt>Cote</dt><dd>{{ $document->cote }}</dd>@endif
                        @if ($document->filiere)<dt>Filière</dt><dd>{{ $document->filiere->nom }}</dd>@endif
                    </dl>
                </div>
                @if ($document->resume)
                    <div class="recap">
                        <div class="recap-entete"><h2>Résumé</h2></div>
                        <p class="p-3 mb-0">{!! nl2br(e($document->resume)) !!}</p>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                @if ($document->estNumerique())
                    <div class="encart encart-foret mb-3">
                        <h2 class="h5"><i class="bi bi-file-earmark-pdf me-1"></i>Version numérique</h2>
                        <p class="small text-white-50 mb-3">
                            PDF{{ $document->taille_fichier ? ' · '.$document->taille_fichier : '' }} ·
                            {{ $document->telechargeable ? 'lecture en ligne et téléchargement' : 'lecture en ligne uniquement' }}
                        </p>
                        @if ($lecteur || auth()->check())
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('bibliotheque.lire', $document) }}" class="btn btn-soleil"><i class="bi bi-book-half me-1"></i>Lire en ligne</a>
                                @if ($document->telechargeable)
                                    <a href="{{ route('bibliotheque.telecharger', $document) }}" class="btn btn-outline-light"><i class="bi bi-download me-1"></i>Télécharger</a>
                                @endif
                            </div>
                        @else
                            <a href="{{ route('bibliotheque.connexion') }}" class="btn btn-soleil"><i class="bi bi-box-arrow-in-right me-1"></i>Se connecter pour lire</a>
                        @endif
                    </div>
                @endif

                {{-- Un document disponible en version numérique se consulte en ligne : pas de demande de prêt papier --}}
                @unless ($document->estNumerique())
                <div class="encart sticky-encart">
                    <h2 class="h5">Exemplaires papier</h2>
                    <div class="mb-3">@include('bibliotheque._disponibilite', ['document' => $document])</div>
                    @if ($enAttente > 0)
                        <p class="small text-gris">{{ $enAttente }} demande{{ $enAttente > 1 ? 's' : '' }} en file d'attente.</p>
                    @endif

                    @error('emprunt')<div class="alert alert-danger small">{{ $message }}</div>@enderror

                    @if (! $document->estEmpruntable())
                        <p class="small mb-0">Ce document se consulte uniquement à la bibliothèque, {{ config('acrest.bibliotheque.horaires') }}.</p>
                    @elseif (! $lecteur)
                        <a href="{{ route('bibliotheque.connexion') }}" class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right me-1"></i>Se connecter pour emprunter</a>
                    @elseif ($dejaDemande)
                        <p class="small mb-2"><i class="bi bi-check-circle text-success me-1"></i>Vous avez déjà une demande ou un emprunt en cours pour ce document.</p>
                        <a href="{{ route('bibliotheque.emprunts') }}" class="btn btn-outline-primary w-100">Voir mes emprunts</a>
                    @else
                        <form method="post" action="{{ route('bibliotheque.demander', $document) }}">
                            @csrf
                            <label for="message" class="form-label small">Message pour la bibliothèque <span class="facultatif">(facultatif)</span></label>
                            <textarea id="message" name="message" rows="2" maxlength="500" class="form-control mb-3" placeholder="Ex. : besoin pour un exposé le 15">{{ old('message') }}</textarea>
                            <button class="btn btn-soleil w-100" data-chargement="Envoi…">
                                <i class="bi bi-bookmark-plus me-1"></i>{{ $document->disponibles_count > 0 ? 'Demander ce document' : 'Réserver (file d\'attente)' }}
                            </button>
                        </form>
                        <p class="small text-gris mt-3 mb-0">
                            Prêt de {{ config('acrest.bibliotheque.duree_pret') }} jours, {{ config('acrest.bibliotheque.max_emprunts') }} documents maximum à la fois.
                            Vous serez prévenu par e-mail quand le document sera prêt à être retiré.
                        </p>
                    @endif
                </div>
                @endunless
            </div>
        </div>
    </div>
</section>
@endsection
