@extends('layouts.admin')
@section('titre', 'Inscriptions')

@section('contenu')
<form method="get" class="admin-carte p-3 mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-4 col-lg-3">
            <label for="q" class="form-label small">Recherche</label>
            <input type="search" id="q" name="q" value="{{ $filtres['q'] ?? '' }}" class="form-control" placeholder="Nom, code, e-mail, téléphone…">
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <label for="filiere" class="form-label small">Filière (1er choix)</label>
            <select id="filiere" name="filiere" class="form-select">
                <option value="">Toutes</option>
                @foreach ($filieres as $f)<option value="{{ $f->id }}" @selected(($filtres['filiere'] ?? '') == $f->id)>{{ $f->nom }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <label for="statut" class="form-label small">Dossier</label>
            <select id="statut" name="statut" class="form-select">
                <option value="">Tous</option>
                @foreach (\App\Enums\StatutInscription::cases() as $s)<option value="{{ $s->value }}" @selected(($filtres['statut'] ?? '') === $s->value)>{{ $s->libelle() }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <label for="paiement" class="form-label small">Frais</label>
            <select id="paiement" name="paiement" class="form-select">
                <option value="">Tous</option>
                <option value="paye" @selected(($filtres['paiement'] ?? '') === 'paye')>Payés</option>
                <option value="non_paye" @selected(($filtres['paiement'] ?? '') === 'non_paye')>Non payés</option>
            </select>
        </div>
        <div class="col-6 col-md-8 col-lg-2 d-flex gap-2">
            <button class="btn btn-primary flex-fill"><i class="bi bi-funnel"></i> Filtrer</button>
            @if (array_filter($filtres))<a href="{{ route('admin.inscriptions.index') }}" class="btn btn-outline-primary" title="Effacer les filtres"><i class="bi bi-x-lg"></i></a>@endif
        </div>
    </div>
</form>

<div class="admin-carte">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-3">
        <span class="text-gris">{{ $inscriptions->total() }} dossier{{ $inscriptions->total() > 1 ? 's' : '' }}</span>
        <a href="{{ route('admin.inscriptions.export', request()->query()) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-filetype-csv me-1"></i>Exporter (Excel/CSV)</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Code</th><th>Candidat</th><th>Contact</th><th>Choix</th><th>Dossier</th><th>Paiement</th><th>Date</th></tr></thead>
            <tbody>
                @forelse ($inscriptions as $i)
                    <tr>
                        <td><a href="{{ route('admin.inscriptions.show', $i) }}" class="fw-semibold text-nowrap">{{ $i->code }}</a></td>
                        <td>{{ $i->nom_complet }}<div class="small text-gris">{{ $i->diplome }} {{ $i->option_diplome }}</div></td>
                        <td class="small">{{ $i->telephone }}<div class="text-gris">{{ $i->email }}</div></td>
                        <td class="small">
                            @foreach ($i->specialites as $s)<div><span class="text-gris">{{ $s->pivot->rang }}.</span> {{ $s->nom }}</div>@endforeach
                        </td>
                        <td><x-statut :statut="$i->statut" /></td>
                        <td>@if ($i->dernierPaiement)<x-statut :statut="$i->dernierPaiement->statut" />@else<span class="small text-gris">Aucun</span>@endif</td>
                        <td class="small text-nowrap">{{ $i->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-4 text-gris">Aucun dossier ne correspond à ces critères.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($inscriptions->hasPages())<div class="p-3">{{ $inscriptions->links() }}</div>@endif
</div>
@endsection
