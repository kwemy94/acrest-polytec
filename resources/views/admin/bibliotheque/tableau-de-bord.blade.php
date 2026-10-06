@extends('layouts.admin')
@section('titre', 'Bibliothèque')

@section('contenu')
@php
    $tuiles = [
        ['Documents', $stats['documents'], 'bi-book', route('admin.documents.index'), ''],
        ['Exemplaires', $stats['exemplaires'], 'bi-upc', route('admin.exemplaires.index'), ''],
        ['Disponibles', $stats['disponibles'], 'bi-check2-circle', route('admin.exemplaires.index', ['statut' => 'disponible']), 'text-success'],
        ['Empruntés', $stats['empruntes'], 'bi-box-arrow-right', route('admin.emprunts.index', ['statut' => 'en_cours']), ''],
        ['Retards', $circulation['retards'], 'bi-exclamation-triangle', route('admin.emprunts.index', ['retard' => 1]), $circulation['retards'] ? 'text-danger' : ''],
        ['Adhérents actifs', $adherentsActifs, 'bi-person-vcard', route('admin.adherents.index', ['statut' => 'actif']), ''],
        ['Documents numériques', $stats['ressources'], 'bi-file-earmark-pdf', route('admin.documents.index', ['numerique' => 1]), ''],
        ['Demandes en ligne', $circulation['demandes'], 'bi-inbox', route('admin.emprunts.index', ['statut' => 'demande']), $circulation['demandes'] ? 'text-warning' : ''],
    ];
@endphp

<div class="d-flex flex-wrap gap-2 mb-3">
    <a href="{{ route('admin.emprunts.create') }}" class="btn btn-primary"><i class="bi bi-box-arrow-right me-1"></i>Nouveau prêt</a>
    <a href="{{ route('admin.emprunts.retours') }}" class="btn btn-soleil"><i class="bi bi-box-arrow-in-left me-1"></i>Enregistrer un retour</a>
    <a href="{{ route('admin.documents.create') }}" class="btn btn-outline-primary"><i class="bi bi-plus-lg me-1"></i>Nouveau document</a>
    <a href="{{ route('admin.adherents.create') }}" class="btn btn-outline-primary"><i class="bi bi-person-plus me-1"></i>Nouvel adhérent</a>
</div>

<div class="row g-3 mb-4">
    @foreach ($tuiles as [$libelle, $nombre, $icone, $lien, $classe])
        <div class="col-6 col-lg-3">
            <a href="{{ $lien }}" class="admin-carte p-3 d-block text-decoration-none text-reset h-100">
                <div class="small text-gris mb-2"><i class="bi {{ $icone }} me-1"></i>{{ $libelle }}</div>
                <div class="admin-chiffre {{ $classe }}">{{ number_format($nombre, 0, ',', ' ') }}</div>
            </a>
        </div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-xl-7">
        <div class="admin-carte h-100">
            <div class="d-flex justify-content-between align-items-center p-3 p-lg-4 pb-0">
                <h2 class="h5 mb-0">Prêts en retard</h2>
                <a href="{{ route('admin.emprunts.index', ['retard' => 1]) }}" class="small">Tout voir</a>
            </div>
            <div class="table-responsive mt-3">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Adhérent</th><th>Document</th><th>Retour prévu</th><th>Retard</th></tr></thead>
                    <tbody>
                        @forelse ($retards as $e)
                            <tr>
                                <td><a href="{{ route('admin.adherents.show', $e->adherent) }}" class="fw-semibold">{{ $e->adherent->nom_complet }}</a><div class="small text-gris">{{ $e->adherent->telephone }}</div></td>
                                <td class="small">{{ $e->document->titre }}<div class="text-gris">{{ $e->exemplaire?->code_inventaire }}</div></td>
                                <td class="small text-nowrap">{{ $e->date_retour_prevue->format('d/m/Y') }}</td>
                                <td><span class="statut statut-danger">{{ $e->joursDeRetard() }} j</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-4 text-gris">Aucun prêt en retard.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="admin-carte h-100">
            <div class="d-flex justify-content-between align-items-center p-3 p-lg-4 pb-0">
                <h2 class="h5 mb-0">Dernières opérations</h2>
                <a href="{{ route('admin.journal') }}" class="small">Historique complet</a>
            </div>
            <ul class="list-unstyled p-3 p-lg-4 mb-0">
                @forelse ($activites as $a)
                    <li class="mb-3 small">
                        <div>{{ $a->description }}</div>
                        <div class="text-gris">{{ $a->created_at->format('d/m/Y H:i') }} · {{ $a->user?->name ?? ($a->adherent ? 'Adhérent '.$a->adherent->matricule : 'Système') }}</div>
                    </li>
                @empty
                    <li class="text-gris">Aucune opération enregistrée.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
