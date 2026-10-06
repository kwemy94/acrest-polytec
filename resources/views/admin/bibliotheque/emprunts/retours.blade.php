@extends('layouts.admin')
@section('titre', 'Retours')

@section('contenu')
<form method="get" class="admin-carte p-3 p-lg-4 mb-3" style="max-width: 760px">
    <label for="code" class="form-label">Code d'inventaire ou code-barres de l'exemplaire rendu</label>
    <div class="input-group input-group-lg">
        <input id="code" name="code" value="{{ $emprunt ? '' : $code }}" class="form-control text-uppercase" required autocomplete="off" @unless ($emprunt) autofocus @endunless placeholder="{{ config('acrest.bibliotheque.prefixe_inventaire') }}00001">
        <button class="btn btn-primary"><i class="bi bi-search"></i> Rechercher</button>
    </div>
    @if ($erreur)<div class="alert alert-warning mt-3 mb-0">{{ $erreur }}</div>@endif
</form>

@if ($emprunt)
    <div class="admin-carte p-3 p-lg-4" style="max-width: 760px">
        <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
            <div>
                <code class="fs-5">{{ $emprunt->exemplaire->code_inventaire }}</code>
                <h2 class="h5 mb-0">{{ $emprunt->document->titre }}</h2>
                <div class="small text-gris">À ranger : {{ $emprunt->exemplaire->localisation?->chemin ?? 'localisation non définie' }}</div>
            </div>
            <div class="text-end">
                <a href="{{ route('admin.adherents.show', $emprunt->adherent) }}" class="fw-semibold">{{ $emprunt->adherent->nom_complet }}</a>
                <div class="small text-gris">{{ $emprunt->adherent->matricule }}</div>
            </div>
        </div>
        <div class="row g-2 small mb-3">
            <div class="col-sm-4"><div class="p-2 rounded bg-light">Prêté le<br><strong>{{ $emprunt->date_pret->format('d/m/Y') }}</strong></div></div>
            <div class="col-sm-4"><div class="p-2 rounded bg-light">Retour prévu<br><strong>{{ $emprunt->date_retour_prevue->format('d/m/Y') }}</strong></div></div>
            <div class="col-sm-4"><div class="p-2 rounded {{ $emprunt->joursDeRetard() ? 'bg-danger-subtle text-danger' : 'bg-success-subtle' }}">Retard<br><strong>{{ $emprunt->joursDeRetard() }} jour(s)</strong></div></div>
        </div>

        <form method="post" action="{{ route('admin.emprunts.retour', $emprunt) }}">
            @csrf @method('PATCH')
            <label class="form-label">État de l'exemplaire au retour</label>
            <div class="d-flex flex-wrap gap-3 mb-2">
                @foreach ($etats as $e)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="etat_physique" id="etat-{{ $e->value }}" value="{{ $e->value }}" @checked($emprunt->exemplaire->etat_physique === $e)>
                        <label class="form-check-label" for="etat-{{ $e->value }}">{{ $e->libelle() }}</label>
                    </div>
                @endforeach
            </div>
            <div class="form-text mb-3">« Endommagé » place l'exemplaire en réparation et ouvre un incident ; sinon il redevient disponible (ou est mis de côté pour la prochaine demande en attente).</div>
            <div class="form-check mb-3">
                <input type="hidden" name="retirer" value="0">
                <input class="form-check-input" type="checkbox" id="retirer" name="retirer" value="1">
                <label class="form-check-label" for="retirer">Retirer l'exemplaire du catalogue</label>
            </div>
            <label for="note" class="form-label small">Observation <span class="facultatif">(facultatif)</span></label>
            <textarea id="note" name="note" rows="2" class="form-control mb-3" maxlength="500" placeholder="Ex. : couverture déchirée"></textarea>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-primary btn-lg"><i class="bi bi-box-arrow-in-left me-1"></i>Enregistrer le retour</button>
            </div>
        </form>

        <details class="mt-4">
            <summary class="small text-danger">L'adhérent signale la perte du document</summary>
            <form method="post" action="{{ route('admin.emprunts.perte', $emprunt) }}" class="mt-2" data-confirmer="Déclarer l'exemplaire {{ $emprunt->exemplaire->code_inventaire }} perdu ? Il ne pourra plus être prêté.">
                @csrf @method('PATCH')
                <input name="note" class="form-control form-control-sm mb-2" placeholder="Circonstances, remboursement prévu…" maxlength="500">
                <button class="btn btn-sm btn-outline-danger">Déclarer perdu</button>
            </form>
        </details>
    </div>
@endif
@endsection
