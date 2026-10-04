@php $v = fn ($champ) => old($champ, $valeurs[$champ] ?? null); @endphp
<form method="post" action="{{ route('inscription.coordonnees') }}" class="needs-validation" novalidate>
    @csrf
    <h2 class="h5 mb-3">Vos coordonnées</h2>
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label for="telephone" class="form-label">Téléphone</label>
            <input type="tel" id="telephone" name="telephone" value="{{ $v('telephone') }}" class="form-control @error('telephone') is-invalid @enderror" required inputmode="tel" autocomplete="tel" placeholder="6 77 00 00 00" pattern="[\d\s+.\-()]{8,20}" autofocus>
            <div class="invalid-feedback">@error('telephone'){{ $message }}@else Saisissez un numéro de téléphone valide. @enderror</div>
        </div>
        <div class="col-md-6">
            <label for="email" class="form-label">Adresse e-mail</label>
            <input type="email" id="email" name="email" value="{{ $v('email') }}" class="form-control @error('email') is-invalid @enderror" required maxlength="150" autocomplete="email" aria-describedby="email-aide">
            <div id="email-aide" class="form-text">Votre code d'inscription y sera envoyé.</div>
            <div class="invalid-feedback">@error('email'){{ $message }}@else Saisissez une adresse e-mail valide. @enderror</div>
        </div>
    </div>

    <h2 class="h5 mb-3">Parents ou tuteur</h2>
    <div class="row g-3">
        <div class="col-md-6">
            <label for="nom_pere" class="form-label">Nom du père <span class="facultatif">facultatif</span></label>
            <input type="text" id="nom_pere" name="nom_pere" value="{{ $v('nom_pere') }}" class="form-control @error('nom_pere') is-invalid @enderror" maxlength="150">
            @error('nom_pere')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label for="nom_mere" class="form-label">Nom de la mère</label>
            <input type="text" id="nom_mere" name="nom_mere" value="{{ $v('nom_mere') }}" class="form-control @error('nom_mere') is-invalid @enderror" required maxlength="150">
            <div class="invalid-feedback">@error('nom_mere'){{ $message }}@else Indiquez le nom de votre mère. @enderror</div>
        </div>
        <div class="col-md-6">
            <label for="contact_parent" class="form-label">Téléphone du parent ou tuteur</label>
            <input type="tel" id="contact_parent" name="contact_parent" value="{{ $v('contact_parent') }}" class="form-control @error('contact_parent') is-invalid @enderror" required inputmode="tel" pattern="[\d\s+.\-()]{8,20}">
            <div class="invalid-feedback">@error('contact_parent'){{ $message }}@else Saisissez un numéro de téléphone valide. @enderror</div>
        </div>
    </div>
    @include('inscription.etapes._actions')
</form>
