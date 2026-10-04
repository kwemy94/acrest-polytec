@php
    $d = $donnees;
    $sexe = isset($d['sexe']) ? \App\Enums\Sexe::tryFrom($d['sexe'])?->libelle() : null;
    $bloc = function ($etape) { return route('inscription.etape', $etape).'?retour=verification'; };
@endphp

<div class="recap">
    <div class="recap-entete">
        <h2><i class="bi bi-person-vcard me-2"></i>Identité</h2>
        <a href="{{ $bloc('identite') }}" class="small"><i class="bi bi-pencil me-1"></i>Modifier</a>
    </div>
    <dl>
        <dt>Nom et prénom</dt><dd>{{ $d['nom'] ?? '' }} {{ $d['prenom'] ?? '' }}</dd>
        <dt>Sexe</dt><dd>{{ $sexe }}</dd>
        <dt>Né(e) le</dt><dd>{{ isset($d['date_naissance']) ? \Illuminate\Support\Carbon::parse($d['date_naissance'])->translatedFormat('d F Y') : '' }} à {{ $d['lieu_naissance'] ?? '' }}</dd>
        <dt>Nationalité</dt><dd>{{ $d['pays'] ?? '' }}</dd>
        <dt>CNI / passeport</dt><dd>{{ $d['cni'] ?? '' }}</dd>
    </dl>
</div>

<div class="recap">
    <div class="recap-entete">
        <h2><i class="bi bi-telephone me-2"></i>Coordonnées</h2>
        <a href="{{ $bloc('coordonnees') }}" class="small"><i class="bi bi-pencil me-1"></i>Modifier</a>
    </div>
    <dl>
        <dt>Téléphone</dt><dd>{{ $d['telephone'] ?? '' }}</dd>
        <dt>E-mail</dt><dd>{{ $d['email'] ?? '' }}</dd>
        <dt>Père</dt><dd>{{ ($d['nom_pere'] ?? null) ?: '—' }}</dd>
        <dt>Mère</dt><dd>{{ $d['nom_mere'] ?? '' }}</dd>
        <dt>Contact parent</dt><dd>{{ $d['contact_parent'] ?? '' }}</dd>
    </dl>
</div>

<div class="recap">
    <div class="recap-entete">
        <h2><i class="bi bi-mortarboard me-2"></i>Diplôme</h2>
        <a href="{{ $bloc('diplome') }}" class="small"><i class="bi bi-pencil me-1"></i>Modifier</a>
    </div>
    <dl>
        <dt>Diplôme</dt><dd>{{ $d['diplome'] ?? '' }} {{ !empty($d['option_diplome']) ? '— '.$d['option_diplome'] : '' }}</dd>
        <dt>Obtenu en</dt><dd>{{ $d['annee_obtention'] ?? '—' }}</dd>
    </dl>
</div>

<div class="recap">
    <div class="recap-entete">
        <h2><i class="bi bi-diagram-3 me-2"></i>Choix de formation</h2>
        <a href="{{ $bloc('formation') }}" class="small"><i class="bi bi-pencil me-1"></i>Modifier</a>
    </div>
    <dl>
        @foreach ($d['choix'] ?? [] as $rang => $choix)
            @php $s = $nomsSpecialites[$choix['specialite']] ?? null; @endphp
            <dt>Choix {{ $rang }}</dt>
            <dd>{{ $s['nom'] ?? '—' }} <span class="fw-normal text-gris">· {{ $s['filiere'] ?? '' }}</span></dd>
        @endforeach
    </dl>
</div>

<form method="post" action="{{ route('inscription.finaliser') }}" class="needs-validation mt-4" novalidate>
    @csrf
    <div class="form-check p-3 ps-5 rounded border @error('certifie') border-danger @enderror bg-light">
        <input class="form-check-input @error('certifie') is-invalid @enderror" type="checkbox" value="1" id="certifie" name="certifie" required>
        <label class="form-check-label" for="certifie">Je certifie l'exactitude des informations ci-dessus et j'ai noté que mon inscription ne sera étudiée qu'après le paiement des frais.</label>
        <div class="invalid-feedback">@error('certifie'){{ $message }}@else Cochez cette case pour continuer. @enderror</div>
    </div>

    <div class="actions-form">
        <a href="{{ route('inscription.etape', 'formation') }}" class="btn btn-outline-primary"><i class="bi bi-arrow-left me-1"></i> Formation</a>
        <button type="submit" class="btn btn-soleil btn-lg" data-chargement="Envoi en cours…"><i class="bi bi-send me-1"></i> Envoyer mon dossier</button>
    </div>
</form>
