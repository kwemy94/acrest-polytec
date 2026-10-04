@extends('layouts.admin')
@section('titre', $document->titre)

@section('contenu')
<a href="{{ route('admin.documents.index') }}" class="small d-inline-block mb-3"><i class="bi bi-arrow-left me-1"></i>Tout le catalogue</a>
<div class="row g-3">
    <div class="col-xl-8">
        <div class="admin-carte p-3 p-lg-4 mb-3">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                <div>
                    <h2 class="h3 mb-1">{{ $document->titre }}</h2>
                    <span class="text-gris">{{ $document->auteurs }}</span>
                </div>
                <div class="align-self-start">@include('bibliotheque._disponibilite', ['document' => $document])</div>
            </div>
            <div class="recap">
                <dl>
                    <dt>Type</dt><dd>{{ $document->type->libelle() }}</dd>
                    <dt>Langue</dt><dd>{{ $document->langue_libelle }}</dd>
                    <dt>Version numérique</dt>
                    <dd>
                        @if ($document->estNumerique())
                            <a href="{{ route('bibliotheque.lire', $document) }}" target="_blank"><i class="bi bi-file-earmark-pdf me-1"></i>Consulter le PDF</a>
                            <span class="fw-normal text-gris">· {{ $document->taille_fichier }} · {{ $document->telechargeable ? 'téléchargeable' : 'lecture en ligne uniquement' }}</span>
                        @else
                            <span class="fw-normal text-gris">Aucune</span>
                        @endif
                    </dd>
                    <dt>Éditeur</dt><dd>{{ $document->editeur ?: '—' }}{{ $document->annee_publication ? ', '.$document->annee_publication : '' }}</dd>
                    <dt>ISBN</dt><dd>{{ $document->isbn ?: '—' }}</dd>
                    <dt>Cote</dt><dd>{{ $document->cote ?: '—' }}</dd>
                    <dt>Filière</dt><dd>{{ $document->filiere?->nom ?? 'Générale' }}</dd>
                    <dt>Prêt</dt><dd>{{ $document->consultation_sur_place ? 'Consultation sur place uniquement' : 'Empruntable' }}</dd>
                </dl>
            </div>
            @if ($document->resume)<p class="mt-3 mb-0">{!! nl2br(e($document->resume)) !!}</p>@endif
        </div>

        <div class="admin-carte">
            <h2 class="h5 p-3 p-lg-4 pb-0 mb-3">Derniers emprunts</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Étudiant</th><th>Exemplaire</th><th>Date</th><th>État</th></tr></thead>
                    <tbody>
                        @forelse ($document->emprunts as $e)
                            <tr>
                                <td>@if ($e->inscription)<a href="{{ route('admin.inscriptions.show', $e->inscription) }}">{{ $e->inscription->nom_complet }}</a><div class="small text-gris">{{ $e->inscription->code }}</div>@endif</td>
                                <td class="small">{{ $e->exemplaire?->code ?? '—' }}</td>
                                <td class="small text-nowrap">{{ $e->created_at->format('d/m/Y') }}</td>
                                <td><x-statut :statut="$e->statut" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-4 text-gris">Ce document n'a jamais été emprunté.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="admin-carte p-3 p-lg-4 mb-3">
            <h2 class="h5 mb-3">Exemplaires</h2>
            <ul class="list-unstyled mb-3">
                @forelse ($document->exemplaires as $ex)
                    <li class="d-flex align-items-center gap-2 py-2 border-bottom">
                        <code>{{ $ex->code }}</code>
                        <x-statut :statut="$ex->etat" class="small" />
                        <div class="ms-auto d-flex gap-1">
                            @if ($ex->etat === \App\Enums\EtatExemplaire::Disponible)
                                <form method="post" action="{{ route('admin.exemplaires.update', $ex) }}" data-confirmer="Retirer l'exemplaire {{ $ex->code }} du prêt (perdu, abîmé…) ?">
                                    @csrf @method('PATCH')
                                    <button name="etat" value="indisponible" class="btn btn-sm btn-outline-secondary" title="Mettre hors prêt"><i class="bi bi-slash-circle"></i></button>
                                </form>
                            @elseif ($ex->etat === \App\Enums\EtatExemplaire::Indisponible)
                                <form method="post" action="{{ route('admin.exemplaires.update', $ex) }}">
                                    @csrf @method('PATCH')
                                    <button name="etat" value="disponible" class="btn btn-sm btn-outline-primary" title="Remettre en rayon"><i class="bi bi-arrow-counterclockwise"></i></button>
                                </form>
                                <form method="post" action="{{ route('admin.exemplaires.destroy', $ex) }}" data-confirmer="Supprimer définitivement l'exemplaire {{ $ex->code }} ?">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Supprimer"><i class="bi bi-trash"></i></button>
                                </form>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="text-gris small">Aucun exemplaire.</li>
                @endforelse
            </ul>
            <form method="post" action="{{ route('admin.documents.exemplaires.store', $document) }}" class="d-flex gap-2">
                @csrf
                <input type="number" name="nombre" value="1" min="1" max="50" class="form-control form-control-sm" style="max-width: 80px" aria-label="Nombre d'exemplaires">
                <button class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>Ajouter des exemplaires</button>
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
