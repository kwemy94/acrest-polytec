@extends('layouts.admin')
@section('titre', $adherent->exists ? 'Modifier l\'adhérent' : 'Nouvel adhérent')

@php
    $creation = ! $adherent->exists;
    $type = old('type', $adherent->type?->value);
    $etudiant = $creation && $type === \App\Enums\TypeAdherent::Etudiant->value;
@endphp

@section('contenu')
<a href="{{ $adherent->exists ? route('admin.adherents.show', $adherent) : route('admin.adherents.index') }}" class="small d-inline-block mb-3"><i class="bi bi-arrow-left me-1"></i>Retour</a>

<form method="post" action="{{ $adherent->exists ? route('admin.adherents.update', $adherent) : route('admin.adherents.store') }}" class="admin-carte p-3 p-lg-4" style="max-width: 760px">
    @csrf
    @if ($adherent->exists) @method('PUT') @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label for="type" class="form-label">Type d'adhérent</label>
            <select id="type" name="type" class="form-select @error('type') is-invalid @enderror">
                @foreach (\App\Enums\TypeAdherent::cases() as $t)<option value="{{ $t->value }}" @selected($type === $t->value)>{{ $t->libelle() }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label for="statut" class="form-label">Statut</label>
            <select id="statut" name="statut" class="form-select @error('statut') is-invalid @enderror">
                @foreach (\App\Enums\StatutAdherent::cases() as $s)<option value="{{ $s->value }}" @selected(old('statut', $adherent->statut?->value) === $s->value)>{{ $s->libelle() }}</option>@endforeach
            </select>
        </div>
    </div>

    {{-- Étudiant : choisi parmi les étudiants en règle --}}
    @if ($creation)
        <fieldset id="bloc-etudiant" class="mt-4" @unless ($etudiant) hidden disabled @endunless>
            <legend class="h6">Étudiant</legend>
            @if ($etudiants->isEmpty())
                <div class="alert alert-warning small mb-0">
                    Aucun étudiant en règle sans fiche adhérent. Un étudiant est « en règle » lorsque son dossier d'inscription est <strong>validé</strong> et ses <strong>frais payés</strong>.
                </div>
            @else
                <label for="recherche-etudiant" class="form-label small">Rechercher parmi les {{ $etudiants->count() }} étudiant(s) en règle</label>
                <input type="search" id="recherche-etudiant" class="form-control mb-2" placeholder="Nom, prénom ou code d'inscription…" autocomplete="off">
                <select id="inscription_id" name="inscription_id" size="8" class="form-select @error('inscription_id') is-invalid @enderror" required aria-describedby="apercu-etudiant">
                    @foreach ($etudiants as $e)
                        <option value="{{ $e->id }}" @selected((string) old('inscription_id') === (string) $e->id)
                                data-code="{{ $e->code }}" data-nom="{{ $e->nom_complet }}" data-email="{{ $e->email }}" data-telephone="{{ $e->telephone }}">
                            {{ $e->nom_complet }} — {{ $e->code }}
                        </option>
                    @endforeach
                </select>
                @error('inscription_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                <div id="apercu-etudiant" class="p-3 rounded bg-light small mt-2" hidden>
                    <div class="fw-semibold" data-champ="nom"></div>
                    <div>Matricule : <code data-champ="code"></code></div>
                    <div class="text-gris"><span data-champ="email"></span> · <span data-champ="telephone"></span></div>
                </div>
                <div class="form-text">Le nom, les coordonnées et le matricule (code d'inscription) sont repris du dossier de l'étudiant.</div>
            @endif
        </fieldset>
    @endif

    {{-- Autres adhérents (ou modification) : saisie de l'identité --}}
    <fieldset id="bloc-identite" class="mt-4" @if ($etudiant) hidden disabled @endif>
        <legend class="h6">Identité</legend>
        <div class="row g-3">
            <div class="col-md-4">
                <label for="matricule" class="form-label">Matricule</label>
                <input id="matricule" name="matricule" value="{{ old('matricule', $adherent->matricule) }}" class="form-control text-uppercase @error('matricule') is-invalid @enderror" required maxlength="30">
            </div>
            <div class="col-md-4">
                <label for="nom" class="form-label">Nom</label>
                <input id="nom" name="nom" value="{{ old('nom', $adherent->nom) }}" class="form-control @error('nom') is-invalid @enderror" required maxlength="255">
            </div>
            <div class="col-md-4">
                <label for="prenom" class="form-label">Prénom</label>
                <input id="prenom" name="prenom" value="{{ old('prenom', $adherent->prenom) }}" class="form-control @error('prenom') is-invalid @enderror" maxlength="255">
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">E-mail</label>
                <input id="email" name="email" type="email" value="{{ old('email', $adherent->email) }}" class="form-control @error('email') is-invalid @enderror" maxlength="255">
                <div class="form-text">Nécessaire pour l'espace en ligne et les notifications.</div>
            </div>
            <div class="col-md-6">
                <label for="telephone" class="form-label">Téléphone</label>
                <input id="telephone" name="telephone" value="{{ old('telephone', $adherent->telephone) }}" class="form-control @error('telephone') is-invalid @enderror" maxlength="20">
            </div>
        </div>
    </fieldset>

    <fieldset class="mt-4">
        <legend class="h6">Adhésion</legend>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="date_inscription" class="form-label">Date d'inscription</label>
                <input id="date_inscription" name="date_inscription" type="date" value="{{ old('date_inscription', $adherent->date_inscription?->toDateString()) }}" class="form-control @error('date_inscription') is-invalid @enderror" required>
            </div>
            <div class="col-md-6">
                <label for="date_expiration" class="form-label">Date d'expiration</label>
                <input id="date_expiration" name="date_expiration" type="date" value="{{ old('date_expiration', $adherent->date_expiration?->toDateString()) }}" class="form-control @error('date_expiration') is-invalid @enderror">
            </div>
            <div class="col-12">
                <label for="notes" class="form-label">Notes</label>
                <textarea id="notes" name="notes" rows="2" class="form-control" maxlength="500">{{ old('notes', $adherent->notes) }}</textarea>
            </div>
        </div>
    </fieldset>

    <div class="mt-4"><button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Enregistrer</button></div>
</form>

@if ($creation)
<script>
(function () {
    var type = document.getElementById('type');
    var blocEtudiant = document.getElementById('bloc-etudiant');
    var blocIdentite = document.getElementById('bloc-identite');
    var liste = document.getElementById('inscription_id');
    var recherche = document.getElementById('recherche-etudiant');
    var apercu = document.getElementById('apercu-etudiant');

    // Étudiant : choix dans la liste ; autres types : saisie manuelle.
    function basculer() {
        var etudiant = type.value === 'etudiant';
        blocEtudiant.hidden = blocEtudiant.disabled = !etudiant;
        blocIdentite.hidden = blocIdentite.disabled = etudiant;
    }

    function afficherApercu() {
        if (!liste || !apercu) return;
        var choix = liste.options[liste.selectedIndex];
        apercu.hidden = !choix;
        if (!choix) return;
        ['nom', 'code', 'email', 'telephone'].forEach(function (champ) {
            apercu.querySelector('[data-champ="' + champ + '"]').textContent = choix.dataset[champ] || '—';
        });
    }

    // Filtre sans accent ni casse sur le libellé de chaque étudiant.
    function normaliser(texte) {
        return texte.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
    }

    if (recherche) {
        recherche.addEventListener('input', function () {
            var terme = normaliser(recherche.value.trim());
            Array.prototype.forEach.call(liste.options, function (option) {
                option.hidden = terme !== '' && normaliser(option.textContent).indexOf(terme) === -1;
            });
        });
    }

    type.addEventListener('change', basculer);
    if (liste) liste.addEventListener('change', afficherApercu);
    basculer();
    afficherApercu();
})();
</script>
@endif
@endsection
