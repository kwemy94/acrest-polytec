@extends('layouts.admin')
@section('titre', 'Paiements')

@section('contenu')
<div class="alert alert-info small">
    <i class="bi bi-info-circle me-1"></i>
    Pour chaque paiement en attente, comparez la référence et le numéro avec l'historique de votre compte marchand Mobile Money avant de confirmer.
    Total confirmé : <strong>{{ number_format($encaisse, 0, ',', ' ') }} FCFA</strong>.
</div>

<form method="get" class="admin-carte p-3 mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-5">
            <label for="q" class="form-label small">Recherche</label>
            <input type="search" id="q" name="q" value="{{ $filtres['q'] ?? '' }}" class="form-control" placeholder="Référence, téléphone, code, nom…">
        </div>
        <div class="col-6 col-md-3">
            <label for="statut" class="form-label small">État</label>
            <select id="statut" name="statut" class="form-select">
                <option value="">Tous</option>
                @foreach ($statuts as $s)<option value="{{ $s->value }}" @selected(($filtres['statut'] ?? '') === $s->value)>{{ $s->libelle() }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label for="operateur" class="form-label small">Opérateur</label>
            <select id="operateur" name="operateur" class="form-select">
                <option value="">Tous</option>
                @foreach ($operateurs as $o)<option value="{{ $o->value }}" @selected(($filtres['operateur'] ?? '') === $o->value)>{{ $o->libelle() }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filtrer</button></div>
    </div>
</form>

<div class="admin-carte">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Date</th><th>Candidat</th><th>Opérateur / numéro</th><th>Référence</th><th>Montant</th><th>État</th><th class="text-end">Action</th></tr></thead>
            <tbody>
                @forelse ($paiements as $p)
                    <tr>
                        <td class="small text-nowrap">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if ($p->inscription)
                                <a href="{{ route('admin.inscriptions.show', $p->inscription) }}" class="fw-semibold">{{ $p->inscription->nom_complet }}</a>
                                <div class="small text-gris">{{ $p->inscription->code }}</div>
                            @endif
                        </td>
                        <td class="small">{{ $p->operateur->libelle() }}<div class="text-gris">{{ $p->telephone }}</div></td>
                        <td><code>{{ $p->reference }}</code></td>
                        <td class="text-nowrap">{{ number_format($p->montant, 0, ',', ' ') }} FCFA</td>
                        <td>
                            <x-statut :statut="$p->statut" />
                            @if ($p->note)<div class="small text-gris mt-1">{{ $p->note }}</div>@endif
                        </td>
                        <td class="text-end">
                            @if ($p->statut === \App\Enums\StatutPaiement::EnAttente)
                                <div class="d-flex gap-1 justify-content-end">
                                    <form method="post" action="{{ route('admin.paiements.traiter', $p) }}" data-confirmer="Confirmer la réception de {{ number_format($p->montant, 0, ',', ' ') }} FCFA (réf. {{ $p->reference }}) ?">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="decision" value="valider">
                                        <button class="btn btn-sm btn-primary"><i class="bi bi-check-lg"></i> Confirmer</button>
                                    </form>
                                    <button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="collapse" data-bs-target="#rejet-{{ $p->id }}" aria-expanded="false"><i class="bi bi-x-lg"></i></button>
                                </div>
                                <form method="post" action="{{ route('admin.paiements.traiter', $p) }}" class="collapse mt-2 text-start" id="rejet-{{ $p->id }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="decision" value="rejeter">
                                    <label for="note-{{ $p->id }}" class="form-label small">Motif du rejet (visible par le candidat)</label>
                                    <textarea id="note-{{ $p->id }}" name="note" rows="2" class="form-control form-control-sm mb-2" required placeholder="Ex. : référence introuvable, montant incomplet"></textarea>
                                    <button class="btn btn-sm btn-danger">Rejeter le paiement</button>
                                </form>
                            @elseif ($p->traite_le)
                                <span class="small text-gris">{{ $p->traite_le->format('d/m/Y') }}{{ $p->agent ? ' · '.$p->agent->name : '' }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-4 text-gris">Aucun paiement ne correspond à ces critères.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($paiements->hasPages())<div class="p-3">{{ $paiements->links() }}</div>@endif
</div>
@endsection
