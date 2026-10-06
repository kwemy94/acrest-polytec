@extends('layouts.admin')
@section('titre', $adherent->nom_complet)

@section('contenu')
<a href="{{ route('admin.adherents.index') }}" class="small d-inline-block mb-3"><i class="bi bi-arrow-left me-1"></i>Tous les adhérents</a>
<div class="row g-3">
    <div class="col-xl-4">
        <div class="admin-carte p-3 p-lg-4 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <code class="fs-5">{{ $adherent->matricule }}</code>
                    <h2 class="h4 mb-0 mt-1">{{ $adherent->nom_complet }}</h2>
                    <div class="text-gris">{{ $adherent->type->libelle() }}</div>
                </div>
                <x-statut :statut="$adherent->statutEffectif()" />
            </div>
            <dl class="small mb-3">
                <dt>E-mail</dt><dd>{{ $adherent->email ?: '—' }}</dd>
                <dt>Téléphone</dt><dd>{{ $adherent->telephone ?: '—' }}</dd>
                <dt>Inscription</dt><dd>{{ $adherent->date_inscription->format('d/m/Y') }}</dd>
                <dt>Expiration</dt><dd>{{ $adherent->date_expiration?->format('d/m/Y') ?? 'Sans limite' }}</dd>
                <dt>Règles de prêt</dt><dd>{{ $quota }} prêts simultanés · {{ $duree }} jours</dd>
                @if ($adherent->inscription)<dt>Dossier d'inscription</dt><dd>{{ $adherent->inscription->code }}</dd>@endif
                @if ($adherent->notes)<dt>Notes</dt><dd>{{ $adherent->notes }}</dd>@endif
            </dl>
            <div class="p-2 rounded bg-light small mb-3">Prêts et demandes en cours : <strong>{{ $actifs }} / {{ $quota }}</strong></div>
            <div class="d-flex flex-wrap gap-2">
                @if ($adherent->estActif())
                    <a href="{{ route('admin.emprunts.create', ['adherent' => $adherent->matricule]) }}" class="btn btn-sm btn-soleil"><i class="bi bi-box-arrow-right me-1"></i>Nouveau prêt</a>
                @endif
                <a href="{{ route('admin.adherents.edit', $adherent) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil me-1"></i>Modifier</a>
            </div>
        </div>
    </div>
    <div class="col-xl-8">
        <div class="admin-carte">
            <h2 class="h5 p-3 p-lg-4 pb-0 mb-3">Historique des prêts</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Document</th><th>Exemplaire</th><th>Prêt</th><th>Retour</th><th>État</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($emprunts as $e)
                            <tr>
                                <td><a href="{{ route('admin.documents.show', $e->document) }}">{{ $e->document->titre }}</a><div class="small text-gris">{{ $e->canal === 'en_ligne' ? 'Demande en ligne' : 'Guichet' }}</div></td>
                                <td class="small">{{ $e->exemplaire?->code_inventaire ?? '—' }}</td>
                                <td class="small text-nowrap">{{ $e->date_pret?->format('d/m/Y') ?? '—' }}</td>
                                <td class="small text-nowrap">
                                    @if ($e->date_retour){{ $e->date_retour->format('d/m/Y') }}@elseif ($e->date_retour_prevue)prévu {{ $e->date_retour_prevue->format('d/m/Y') }}@else — @endif
                                </td>
                                <td>
                                    <x-statut :statut="$e->statut" />
                                    @if ($e->joursDeRetard())<div class="small text-danger">{{ $e->joursDeRetard() }} j de retard</div>@endif
                                </td>
                                <td class="text-end">
                                    @if ($e->statut === \App\Enums\StatutEmprunt::EnCours)
                                        <a href="{{ route('admin.emprunts.retours', ['code' => $e->exemplaire->code_inventaire]) }}" class="btn btn-sm btn-primary">Retour</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-4 text-gris">Aucun prêt.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
