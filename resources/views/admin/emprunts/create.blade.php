@extends('layouts.admin')
@section('titre', 'Prêt au guichet')

@section('contenu')
<a href="{{ route('admin.emprunts.index') }}" class="small d-inline-block mb-3"><i class="bi bi-arrow-left me-1"></i>Tous les emprunts</a>

<form method="post" action="{{ route('admin.emprunts.store') }}" class="admin-carte p-3 p-lg-4" style="max-width: 560px">
    @csrf
    <p class="text-gris small">
        Enregistrez un prêt directement au comptoir. Si l'étudiant avait déjà demandé ce document, sa demande est servie.
        Le prêt dure {{ config('acrest.bibliotheque.duree_pret') }} jours ; l'étudiant reçoit une confirmation par e-mail.
    </p>
    <div class="mb-3">
        <label for="code" class="form-label">Code d'inscription de l'étudiant</label>
        <input id="code" name="code" value="{{ old('code') }}" class="form-control text-uppercase @error('code') is-invalid @enderror" required autofocus autocomplete="off" placeholder="ISAP-26-XXXXXX">
    </div>
    <div class="mb-4">
        <label for="exemplaire" class="form-label">Code-barres de l'exemplaire</label>
        <input id="exemplaire" name="exemplaire" value="{{ old('exemplaire') }}" class="form-control text-uppercase @error('exemplaire') is-invalid @enderror" required autocomplete="off" placeholder="EX000123">
    </div>
    <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer le prêt</button>
</form>
@endsection
