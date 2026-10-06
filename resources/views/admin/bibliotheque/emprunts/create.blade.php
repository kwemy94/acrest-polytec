@extends('layouts.admin')
@section('titre', 'Nouveau prêt')

@section('contenu')
<div class="row g-3">
    <div class="col-lg-7">
        <form method="post" action="{{ route('admin.emprunts.store') }}" class="admin-carte p-3 p-lg-4">
            @csrf
            <div class="mb-3">
                <label for="matricule" class="form-label">Matricule de l'adhérent</label>
                <input id="matricule" name="matricule" value="{{ old('matricule', $matricule) }}" class="form-control form-control-lg text-uppercase @error('matricule') is-invalid @enderror" required autocomplete="off" @if (! old('matricule', $matricule)) autofocus @endif>
            </div>
            <div class="mb-4">
                <label for="exemplaire" class="form-label">Code d'inventaire ou code-barres de l'exemplaire</label>
                <input id="exemplaire" name="exemplaire" value="{{ $exemplaire }}" class="form-control form-control-lg text-uppercase @error('exemplaire') is-invalid @enderror" required autocomplete="off" placeholder="{{ config('acrest.bibliotheque.prefixe_inventaire') }}00001" @if (old('matricule', $matricule)) autofocus @endif>
                <div class="form-text">Compatible avec une douchette : scannez puis validez.</div>
            </div>
            <button class="btn btn-primary btn-lg"><i class="bi bi-check-lg me-1"></i>Enregistrer le prêt</button>
        </form>
    </div>
    <div class="col-lg-5">
        <div class="admin-carte p-3 p-lg-4 small">
            <h2 class="h6">Contrôles effectués</h2>
            <ul class="mb-0">
                <li>l'adhérent est actif (ni suspendu, ni expiré) et n'a pas de prêt en retard ;</li>
                <li>il n'a pas atteint sa limite de prêts ;</li>
                <li>l'exemplaire est disponible (ou réservé pour lui) et n'est pas déjà emprunté ;</li>
                <li>le document n'est pas en consultation sur place.</li>
            </ul>
            <p class="mt-2 mb-0">L'exemplaire passe automatiquement au statut <strong>Emprunté</strong> et l'adhérent reçoit la date de retour par e-mail.</p>
        </div>
    </div>
</div>
@endsection
