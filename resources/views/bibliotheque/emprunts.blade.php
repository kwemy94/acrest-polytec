@extends('layouts.app')
@section('titre', 'Mes emprunts')

@section('contenu')
@include('partials.entete', [
    'titre' => 'Mes emprunts',
    'intro' => 'Suivez vos demandes, vos prêts en cours et leurs dates de retour.',
    'ariane' => ['Bibliothèque' => route('bibliotheque.index'), 'Mes emprunts' => null],
])

<section class="section">
    <div class="container" style="max-width: 960px">
        @include('partials.flash')
        @error('emprunt')<div class="alert alert-danger">{{ $message }}</div>@enderror
        @include('bibliotheque._lecteur', ['lecteur' => $lecteur])

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 mb-0">En cours</h2>
            <a href="{{ route('bibliotheque.index') }}" class="btn btn-sm btn-soleil"><i class="bi bi-plus-lg me-1"></i>Demander un document</a>
        </div>

        @forelse ($actifs as $e)
            <div class="encart mb-3">
                <div class="d-flex flex-column flex-md-row gap-3 justify-content-between">
                    <div>
                        <a href="{{ route('bibliotheque.show', $e->document) }}" class="fw-semibold fs-5 text-decoration-none">{{ $e->document->titre }}</a>
                        <div class="small text-gris">{{ $e->document->auteurs }}{{ $e->exemplaire ? ' · Exemplaire '.$e->exemplaire->code : '' }}</div>
                        <div class="mt-2 d-flex flex-wrap gap-2 align-items-center">
                            <x-statut :statut="$e->statut" />
                            @if ($e->estEnRetard())
                                <span class="statut statut-danger"><i class="bi bi-exclamation-triangle-fill" style="font-size:.8rem"></i> {{ $e->joursDeRetard() }} jour(s) de retard</span>
                            @endif
                        </div>
                        <p class="small mt-2 mb-0">
                            @switch($e->statut)
                                @case(\App\Enums\StatutEmprunt::Demande)
                                    Demandé le {{ $e->created_at->format('d/m/Y') }}. Vous serez prévenu par e-mail dès qu'un exemplaire sera mis de côté.
                                    @break
                                @case(\App\Enums\StatutEmprunt::Reserve)
                                    <strong>À retirer avant le {{ $e->retirer_avant->translatedFormat('d F Y') }}</strong> au comptoir de la bibliothèque ({{ config('acrest.bibliotheque.horaires') }}).
                                    @break
                                @case(\App\Enums\StatutEmprunt::EnCours)
                                    Emprunté le {{ $e->date_pret->format('d/m/Y') }} · <strong>à rendre le {{ $e->date_retour_prevue->translatedFormat('d F Y') }}</strong>
                                    @if ($e->prolongations) · prolongé {{ $e->prolongations }} fois @endif
                                    @break
                            @endswitch
                        </p>
                    </div>
                    <div class="d-flex flex-md-column gap-2 align-items-md-end flex-shrink-0">
                        @if ($e->peutEtreProlonge())
                            <form method="post" action="{{ route('bibliotheque.prolonger', $e) }}" data-confirmer="Prolonger ce prêt de {{ config('acrest.bibliotheque.duree_pret') }} jours ?">
                                @csrf
                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-calendar-plus me-1"></i>Prolonger</button>
                            </form>
                        @endif
                        @if ($e->peutEtreAnnule())
                            <form method="post" action="{{ route('bibliotheque.annuler', $e) }}" data-confirmer="Annuler cette demande ?">
                                @csrf
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg me-1"></i>Annuler</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="encart text-center p-4 mb-3">
                <p class="mb-0 text-gris">Aucun emprunt ni demande en cours.</p>
            </div>
        @endforelse

        <p class="small text-gris">
            Règles de prêt : {{ config('acrest.bibliotheque.max_emprunts') }} documents maximum à la fois, pour {{ config('acrest.bibliotheque.duree_pret') }} jours,
            prolongeables {{ config('acrest.bibliotheque.max_prolongations') }} fois si personne n'attend le document.
            Un document réservé doit être retiré sous {{ config('acrest.bibliotheque.delai_retrait') }} jours.
        </p>

        @if ($historique->isNotEmpty())
            <h2 class="h4 mt-5 mb-3">Historique</h2>
            <div class="recap">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead><tr><th>Document</th><th>Demandé le</th><th>Rendu le</th><th>État</th></tr></thead>
                        <tbody>
                            @foreach ($historique as $e)
                                <tr>
                                    <td>{{ $e->document->titre }}</td>
                                    <td class="text-nowrap">{{ $e->created_at->format('d/m/Y') }}</td>
                                    <td class="text-nowrap">{{ $e->date_retour?->format('d/m/Y') ?? '—' }}</td>
                                    <td>
                                        <x-statut :statut="$e->statut" />
                                        @if ($e->motif)<div class="small text-gris mt-1">{{ $e->motif }}</div>@endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
