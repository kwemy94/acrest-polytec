@extends('layouts.admin')
@section('titre', 'Tableau de bord')

@section('contenu')
@php $max = max(1, $parFiliere->max('total') ?? 1); @endphp
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.inscriptions.index') }}" class="admin-carte p-3 d-block text-decoration-none text-reset h-100">
            <div class="small text-gris mb-2"><i class="bi bi-people me-1"></i>Inscriptions</div>
            <div class="admin-chiffre">{{ $total }}</div>
            <div class="small mt-2 text-gris">{{ $parStatut['en_attente'] }} en attente · {{ $parStatut['validee'] }} validées</div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.paiements.index', ['statut' => 'en_attente']) }}" class="admin-carte p-3 d-block text-decoration-none text-reset h-100">
            <div class="small text-gris mb-2"><i class="bi bi-hourglass-split me-1"></i>Paiements à vérifier</div>
            <div class="admin-chiffre {{ $paiementsEnAttente ? 'text-warning' : '' }}">{{ $paiementsEnAttente }}</div>
            <div class="small mt-2 text-gris">Déclarés par les candidats</div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <div class="admin-carte p-3 h-100">
            <div class="small text-gris mb-2"><i class="bi bi-cash-stack me-1"></i>Encaissé</div>
            <div class="admin-chiffre">{{ number_format($encaisse, 0, ',', ' ') }}</div>
            <div class="small mt-2 text-gris">FCFA confirmés</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.newsletter.index') }}" class="admin-carte p-3 d-block text-decoration-none text-reset h-100">
            <div class="small text-gris mb-2"><i class="bi bi-envelope-paper me-1"></i>Abonnés newsletter</div>
            <div class="admin-chiffre">{{ $abonnes }}</div>
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-xl-5">
        <div class="admin-carte p-3 p-lg-4 h-100">
            <h2 class="h5 mb-3">Premiers choix par filière</h2>
            @forelse ($parFiliere as $ligne)
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1"><span>{{ $ligne->nom }}</span><strong>{{ $ligne->total }}</strong></div>
                    <div class="barre"><span style="width: {{ round($ligne->total / $max * 100) }}%"></span></div>
                </div>
            @empty
                <p class="text-gris mb-0">Aucune inscription pour le moment. Les choix des candidats apparaîtront ici.</p>
            @endforelse
        </div>
    </div>
    <div class="col-xl-7">
        <div class="admin-carte h-100">
            <div class="d-flex justify-content-between align-items-center p-3 p-lg-4 pb-0">
                <h2 class="h5 mb-0">Dernières inscriptions</h2>
                <a href="{{ route('admin.inscriptions.index') }}" class="small">Tout voir</a>
            </div>
            <div class="table-responsive mt-3">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>Candidat</th><th>1er choix</th><th>Paiement</th><th>Date</th></tr></thead>
                    <tbody>
                        @forelse ($dernieres as $i)
                            <tr>
                                <td><a href="{{ route('admin.inscriptions.show', $i) }}" class="fw-semibold text-decoration-none">{{ $i->nom_complet }}</a><div class="small text-gris">{{ $i->code }}</div></td>
                                <td class="small">{{ $i->specialites->first()?->nom }}</td>
                                <td>@if ($i->dernierPaiement)<x-statut :statut="$i->dernierPaiement->statut" />@else<span class="small text-gris">Aucun</span>@endif</td>
                                <td class="small text-nowrap">{{ $i->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-gris p-4">Aucune inscription pour le moment.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
